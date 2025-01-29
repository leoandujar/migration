<?php

namespace App\MessageHandler;

use App\Linker\Services\RedisClients;
use App\Message\WorkflowRunMessage;
use App\Message\WorkflowScheduleRunMessage;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\Workflow;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Model\Repository\WorkflowRepository;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class WorkflowScheduleRunMessageHandler
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private EntityManagerInterface $em;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationSrv;

	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		EntityManagerInterface $em,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationSrv,
		MessageBusInterface $bus,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->parameterBag = $parameterBag;
		$this->notificationSrv = $notificationSrv;
		$this->bus = $bus;
	}

	public function __invoke(WorkflowScheduleRunMessage $message): void
	{
		do {
			$this->loggerSrv->addInfo('PROCESSING WORKFLOW QUEUE.');
			$dequeueLimit = self::LIMIT;
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS)) !== null) {
				try {
					if (($fileObj = json_decode($payload)) === false) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}

					$this->loggerSrv->addInfo('Entity found...processing.');

					$projectPath = "{$this->parameterBag->get('kernelProjectDir')}/bin/console";
					/** @var AVWorkflowMonitor $wfMonitorObj */
					$wfMonitorObj = $this->em->getRepository(Workflow::class)->find($fileObj->id);
					if (!$wfMonitorObj) {
						$this->loggerSrv->addWarning("Workflow Monitor not found for id $fileObj->id. Added to queue again.");
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS, json_encode(['id' => $fileObj->id]));
						$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FAILED);
						continue;
					}
					$wfObject = $this->em->getRepository(Workflow::class)->find($wfMonitorObj->getWorkflow()->getId());
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
					try {
						$this->bus->dispatch(new WorkflowRunMessage($wfName, $fileObj->id));
					} catch (\Throwable $thr) {
						$this->loggerSrv->addCritical('Workflow queue process finished unexpectedly.');
						$this->updateMonitor($fileObj->id, AVWorkflowMonitor::STATUS_FAILED, [$thr->getMessage()]);
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
		$wfMonitor = $this->em->getRepository(Workflow::class)->find($wfMonitorId);
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
