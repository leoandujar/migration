<?php

namespace App\Workflow\Services\XtmGithub;

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
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public function run($name, WFParams $params = null): void
	{
		try {
			$this->loggerSrv->addInfo('Starting github workflow.');
			$workflow = $this->workflowRepository->findOneBy(['name' => $name]);
			if (null === $params) {
				$params = $workflow->getParameters();
			}
			if (!array_key_exists('owner', $params->getParams())) {
				$this->loggerSrv->addError('The github username was not configured');

				return;
			}
			if (!array_key_exists('repository', $params->getParams())) {
				$this->loggerSrv->addError('The github repository was not configured');

				return;
			}
			if (!array_key_exists('token', $params->getParams())) {
				$this->loggerSrv->addError('The github token was not configured');

				return;
			}
			if (!array_key_exists('path', $params->getParams())) {
				$this->loggerSrv->addError('The github path was not configured');

				return;
			}

			$history = new WFHistory();
			$history->setCreatedAt(new \DateTime());
			if (isset($params->getParams()['project_id'])) {
				$history->setName($name);
			}
			$this->loggerSrv->addInfo(sprintf('Starting github workflow for project %s', $params->getParams()['project_id']));
			$history->setInfo(sprintf('Date: %s', $history->getCreatedAt()->format('Y-m-d H:i:s')));
			$history->setWorkflowId($workflow->getId());
			$history->setName($workflow->getName());
			$history->setRemoved(false);
			$this->loggerSrv->addInfo('Fetching the latest commit from repository.');
			$registryWorkflow = $this->registry->get($history, 'github');
			$notificationTarget = $params->getNotificationTarget();
			$parameter = $params->getParams();
			$history->setContext(array_merge(
				[
					'source_disk' => $parameter['source_disk'],
					'working_disk' => $parameter['working_disk'],
					'notification_target' => $notificationTarget,
					'notification_type' => $params->getNotificationType(),
					'files' => [],
				],
				$parameter
			));
			if ($registryWorkflow->can($history, 'initialized')) {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$registryWorkflow->apply(
					$history,
					'initialized'
				);
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting github workflow', $thr);
			throw $thr;
		}
	}
}
