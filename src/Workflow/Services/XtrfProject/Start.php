<?php

namespace App\Workflow\Services\XtrfProject;

use App\Model\Repository\WorkflowMonitorRepository;
use App\Workflow\HelperServices\ProjectWorkflowService;
use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFHistory;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\WorkflowRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Start implements WorkflowInterface
{
	public const TYPE_PROJECT = 'project';
	public const TYPE_QUOTE = 'quote';

	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private WorkflowRepository $workflowRepository;
	private ProjectWorkflowService $projectWorkflowSrv;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		WorkflowRepository $workflowRepository,
		WorkflowMonitorRepository $wfMonitorRepo,
		ProjectWorkflowService $projectWorkflowSrv,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->workflowRepository = $workflowRepository;
		$this->projectWorkflowSrv = $projectWorkflowSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	public function run($name, WFParams $parameter = null): void
	{
		try {
			$this->loggerSrv->addInfo("Fetching Workflow $name.");
			$workflow = $this->workflowRepository->findOneBy(['name' => $name]);
			if (null === $workflow && !$parameter) {
				$msg = "Workflow $name not found";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}
			$this->loggerSrv->addInfo("Starting project workflow: $name");
			$history = new WFHistory();
			$parameter = $parameter ?? $workflow->getParams();
			$history->setCreatedAt(new \DateTime());
			$history->setWorkflowId($workflow?->getId());
			$history->setName($name);
			$history->setInfo(sprintf('Date: %s', $history->getCreatedAt()->format('Y-m-d H:i:s')));
			$history->setRemoved(false);
			$registryWorkflow = $this->registry->get($history, 'xtrf_project');
			$params = $this->projectWorkflowSrv->prepareData($parameter, $name);
			$history->setContext($params);

			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
			$registryWorkflow->apply($history, 'initialized');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error starting xtrf project workflow', $thr);
			throw $thr;
		}
	}
}
