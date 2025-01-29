<?php

namespace App\Workflow\Services\XtrfQbo;

use App\Service\UtilService;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\WorkflowRepository;

class Start implements WorkflowInterface
{
	private Registry $registry;
	private UtilService $utilsSrv;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private WorkflowRepository $workflowRepository;
	private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		Registry $registry,
		UtilService $utilsSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		WorkflowRepository $workflowRepository,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->utilsSrv = $utilsSrv;
		$this->loggerSrv = $loggerSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->workflowRepository = $workflowRepository;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_QBO);
	}

	public function run(string $name, WFParams $parameter = null): void
	{
		$this->loggerSrv->addInfo('Starting XTRF-QBO workflow.');

		/** @var WFWorkflow $workflow */
		$workflow = $this->workflowRepository->findOneBy(['name' => $name]);

		$this->loggerSrv->addInfo('Checking if data param is present');
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
		try {
			$registryWorkflow = $this->registry->get($history, 'wf_xtrf_qbo');

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
				return;
			}
			if (!empty($params['filters']['final_date']) && !is_array($params['filters']['final_date'])) {
				$params['filters']['final_date'] = [
					$this->utilsSrv->getDateByFormat($params['filters']['final_date'])->format('d/m/Y'),
					(new \DateTime('now'))->format('d/m/Y'),
				];
			}
			$history->setContext($params);
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
			$registryWorkflow->apply($history, 'start');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting Xtrf-Qbo workflow.', $thr);
		}
	}

	private function valid(array $params): bool
	{
		if (!array_key_exists('filters', $params)) {
			$this->loggerSrv->addError('filters parameter not defined');

			return false;
		}
		$mandatoryFilters = $params['filters']['search'] ? ['search'] : ['customer_id', 'status', 'final_date'];
		$diff = array_diff(array_keys($params['filters']), $mandatoryFilters);
		if (count($diff)) {
			foreach ($diff as $item) {
				$this->loggerSrv->addError("mandatory param $item is not defined in the filters.");
			}

			return false;
		}

		return true;
	}
}
