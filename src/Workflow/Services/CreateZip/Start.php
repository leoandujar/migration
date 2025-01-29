<?php

namespace App\Workflow\Services\CreateZip;

use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFHistory;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\WorkflowRepository;

class Start implements WorkflowInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private WorkflowRepository $workflowRepository;
	private EntityManagerInterface $em;

	/**
	 * Start constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		WorkflowRepository $workflowRepository,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->workflowRepository = $workflowRepository;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ZIP_CREATE);
	}

	public function run($name, WFParams $params = null): void
	{
		try {
			$this->loggerSrv->addInfo('Starting create zip workflow.');
			$workflow = $this->workflowRepository->findOneBy(['name' => $name]);
			if (null === $workflow) {
				$this->loggerSrv->alert(sprintf('No workflow named %s was founded', $name));

				return;
			}
			$history = new WFHistory();
			$history->setCreatedAt(new \DateTime());
			$history->setWorkflowId($workflow->getId());
			$history->setName($workflow->getName());
			$history->setInfo(sprintf('Date: %s', $history->getCreatedAt()->format('Y-m-d H:i:s')));
			$history->setExpiresAt((new \DateTime())->add(new \DateInterval(sprintf('P%dD', $params->getExpiration()))));
			$history->setRemoved(false);
			$registryWorkflow = $this->registry->get($history, 'create_zip');
			$notificationTarget = $params->getNotificationTarget();
			$parameter = $params->getParams();
			$history->setContext(array_merge(
				[
					'statistics' => [
						'processedFiles' => 0,
						'totalFiles' => 0,
						'errorFiles' => 0,
					],
					'source_disk' => $parameter['source_disk'],
					'working_disk' => $parameter['working_disk'],
					'notification_target' => $notificationTarget,
					'notification_type' => $params->getNotificationType(),
					'files' => [],
				],
				$parameter
			));
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
			if ($registryWorkflow->can($history, 'initialized')) {
				$registryWorkflow->apply($history, 'initialized');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting create zip workflow', $thr);
			throw $thr;
		}
	}
}
