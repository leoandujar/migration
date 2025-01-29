<?php

namespace App\Workflow\Services\XtmProject;

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
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_PROJECT);
	}

	public function run($name, WFParams $parameters = null): void
	{
		$this->loggerSrv->addInfo("Starting xtm project workflow $name.");
		$workflow = $this->workflowRepository->findOneBy(['name' => $name]);
		if (null === $workflow) {
			return;
		}
		$request = new WFHistory();
		$parameters = $parameters ?? $workflow->getParameters();
		$request->setCreatedAt(new \DateTime());
		$request->setWorkflowId($workflow->getId());
		$request->setName($workflow->getName());
		$request->setInfo(sprintf('Date: %s', $request->getCreatedAt()->format('Y-m-d H:i:s')));
		$request->setRemoved(false);
		try {
			$registryWorkflow = $this->registry->get($request, 'xtm_project');
			$notificationTarget = null;
			$params = [];
			if (null !== $params) {
				$params = $parameters->getParams();
				$params['projects'] = [];
				$params['notification_type'] = $parameters->getNotificationType();
				$params['notification_target'] = $parameters->getNotificationTarget();
			}
			$request->setContext($params);
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($request);
			$this->em->flush();
			$registryWorkflow->apply(
				$request,
				'initialized'
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting XtmProject workflow.', $thr);
		}
	}
}
