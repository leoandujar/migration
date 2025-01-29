<?php

namespace App\Command\Services;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Model\Repository\WorkflowRepository;
use App\Service\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WorkflowProcessQueueService
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private EntityManagerInterface $em;
	private WorkflowRepository $workflowRepo;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		EntityManagerInterface $em,
		WorkflowRepository $workflowRepo,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->parameterBag = $parameterBag;
		$this->workflowRepo = $workflowRepo;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->notificationSrv = $notificationSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function dequeueAndProcess(OutputInterface $output): void
	{
		do {
			$output->writeln('PROCESSING WORKFLOW QUEUE.');
			$dequeueLimit = self::LIMIT;
			$runningProcesses = [];
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS)) !== null) {
				try {
					if (($fileObj = json_decode($payload)) === false) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}

					$output->writeln('Entity found...processing.');

					$projectPath = "{$this->parameterBag->get('kernelProjectDir')}/bin/console";
					/** @var AVWorkflowMonitor $wfMonitorObj */
					$wfMonitorObj = $this->wfMonitorRepo->find($fileObj->id);
					if (!$wfMonitorObj) {
						$this->loggerSrv->addWarning("Workflow Monitor not found for id $fileObj->id. Added to queue again.");
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS, json_encode(['id' => $fileObj->id]));
						$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FAILED);
						continue;
					}
					$wfObject = $this->workflowRepo->find($wfMonitorObj->getWorkflow()->getId());
					if (!$wfObject) {
						$this->loggerSrv->addWarning("Workflow queue could not find workflow with id {$wfMonitorObj->getWorkflow()?->getId()} for monitor $fileObj->id. Added to queue again.");
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS, json_encode(['id' => $fileObj->id]));
						$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FAILED);
						continue;
					}

					$wfMonitorObj
						->setStartedAt(new \DateTime('now'))
						->setStatus(AVWorkflowMonitor::STATUS_RUNNING);
					$this->em->persist($wfMonitorObj);
					$this->em->flush();
					$wfName = $wfObject->getName();
					$this->loggerSrv->addInfo('Workflow '.$wfName.' dequeue and starting '.$wfMonitorObj->getId());
					$process = new Process([
						'php',
						$projectPath,
						'workflow:run',
						"$wfName",
						"$fileObj->id",
					], null, null, null, 1800);
					try {
						$process->mustRun();
					} catch (ProcessFailedException $ex) {
						$this->loggerSrv->addCritical('Workflow queue process finished unexpectedly.');
						$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FAILED, [$ex->getMessage()]);
					}
					$runningProcesses[] = $process;
					while (count($runningProcesses)) {
						foreach ($runningProcesses as $i => $runningProcess) {
							if (!$runningProcess->isRunning()) {
								$output->writeln('PROCESS END.');
								unset($runningProcesses[$i]);
								$this->em->refresh($wfMonitorObj);
								if (AVWorkflowMonitor::STATUS_FAILED !== $wfMonitorObj->getStatus()) {
									if (null !== $wfMonitorObj->getAuxiliaryData() && count($wfMonitorObj->getAuxiliaryData())) {
										$this->em->refresh($wfMonitorObj);
										$wfMonitorObj->setStatus(AVWorkflowMonitor::STATUS_RUNNING);
										$this->em->persist($wfMonitorObj);
										$this->em->flush();
										$this->loggerSrv->addWarning("Workflow Monitor with id $fileObj->id was processed but remains items to process. Added to queue again.");
										$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS, json_encode(['id' => $fileObj->id]));
									} else {
										$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FINISHED);
									}
								}
							}
						}

						// check every second
                        usleep(1000000);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Workflow entity data. Check logs for more details.', $thr);
					if (null !== $fileObj->id) {
						$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FAILED, [$thr->getMessage()]);
					}
					continue;
				}
			}
		} while (0);
	}

	private function updateMonitor(string $wfMonitorId, $status, array $details = []): void
	{
		/** @var AVWorkflowMonitor $wfMonitor */
		$wfMonitor = $this->wfMonitorRepo->find($wfMonitorId);
		$this->em->refresh($wfMonitor);
		if ($wfMonitor) {
			$wfDetails = $wfMonitor->getDetails() ?? [];
			$wfMonitor
				->setFinishedAt(new \DateTime('now'))
				->setStatus($status);
			if (AVWorkflowMonitor::STATUS_FAILED === $status) {
				$errors = $wfDetails['errors'] ?? [];
				$errors[] = $details;
				$wfDetails['errors'] = $errors;
				$notificationTarget = $wfMonitor->getWorkflow()?->getParameters()?->getNotificationTarget();
				$notificationType = $wfMonitor->getWorkflow()?->getParameters()?->getNotificationType();
				if ($notificationTarget && $notificationType) {
					$this->notificationSrv->addNotification($notificationType, $notificationTarget, [
						'status' => 'failure',
						'title' => 'Workflow failed.',
						'message' => "Workflow {$wfMonitor->getWorkflow()->getName()} failed. Check logs for more details.",
					], 'Workflow failed.');
				}
				$this->loggerSrv->addInfo('WFMonitor data here', $wfDetails);
				$this->loggerSrv->addCritical('WFMonitor failed due error.', $details);
			}
			$wfMonitor->setDetails($wfDetails);
			$this->em->persist($wfMonitor);
			$this->em->flush();
		}
	}
}
