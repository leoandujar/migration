<?php

namespace App\Workflow\Services\XtrfProjectV2;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
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
	private WorkflowMonitorRepository $wfMonitorRepo;

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
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	public function run($name, WFParams $parameter = null): void
	{
		$this->loggerSrv->addInfo("Starting workflow $name.");

		/** @var WFWorkflow $workflow */
		$workflow = $this->workflowRepository->findOneBy(['name' => $name]);

		$this->loggerSrv->addInfo("Checking if data param is present for workflow $name");
		if (null === $workflow) {
			$msg = "Workflow $name not found";
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		$parameter = $parameter ?? $workflow->getParameters();
		if (null === $parameter) {
			$msg = "Workflow $name has not params.";
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		$params = $parameter->getParams();
		$history = WFHistory::instance($workflow);

		$wf = $this->registry->get($history, 'xtrf_project_v2');

		try {
			if (empty($params['monitor_id'])) {
				$msg = "Workflow $name has not monitor ID associated. Unable to continue";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			$monitorID = $params['monitor_id'];
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($monitorID);
			if (null === $monitorObj) {
				$msg = "Workflow $name could not found the monitor with id $monitorID on DB. Unable to continue";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}
			if (null === $monitorObj) {
				$msg = "Workflow $name could not found the monitor with id $monitorID on DB. Unable to continue";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			if (null !== $monitorObj->getDetails() && !empty($monitorObj->getDetails()['params'])) {
				$params = $monitorObj->getDetails()['params'];
				$params['monitor_id'] = $monitorID;
			}

			if (!$this->valid($params)) {
				$wf->apply($history, 'finish');

				return;
			}

			$xtrfUser = $monitorObj->getCreatedBy()?->getXtrfUser();
			if ($xtrfUser) {
				$params['template']['project_coordinator'] = intval($xtrfUser->getId());
			}

			$params = $this->projectWorkflowSrv->prepareData($parameter, $name, $params);

			$history->setContext($params);
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
			$wf->apply($history, 'initialize');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error starting xtrf project workflow', $thr);
			if ($wf->can($history, 'finish')) {
				$wf->apply($history, 'finish');
			}

			return;
		}
	}

	private function valid(array $params): bool
	{
		$mandatoryParams = ['type', 'template', 'sourceDisk', 'sourcePath', 'ocr'];
		$diff = array_diff($mandatoryParams, array_keys($params));
		if (count($diff)) {
			foreach ($diff as $item) {
				$this->loggerSrv->addError("mandatory param $item is not defined");
			}

			return false;
		}
		if (!in_array($params['type'], [self::TYPE_PROJECT, self::TYPE_QUOTE])) {
			$this->loggerSrv->addError('type param is not valid');

			return false;
		}
		if ($params['ocr'] && !in_array($params['ocr'], ['type', 'config'])) {
			$this->loggerSrv->addError('ocr is not valid');

			return false;
		}

		return true;
	}
}
