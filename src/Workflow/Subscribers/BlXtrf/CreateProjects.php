<?php

namespace App\Workflow\Subscribers\BlXtrf;

use App\Connector\Xtrf\XtrfConnector;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\Task;
use App\Model\Entity\WFHistory;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;

class CreateProjects implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private XtrfConnector $xtrfConnector;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		XtrfConnector $xtrfConnector,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->xtrfConnector = $xtrfConnector;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_BL_XTRF);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.wf_bl_xtrf.completed.collect' => 'createProjects',
		];
	}

	public function createProjects(Event $event)
	{
		$this->loggerSrv->addInfo('Starting create projects in XTRF');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		if ($context['monitor_id']) {
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
		}
		$xtrfRequests = $context['xtrfRequests'];
		try {
			foreach ($xtrfRequests as $index => $xtrfRequest) {
				$projectCreateResponse = $this->xtrfConnector->createProject($xtrfRequest['projectRequests']);
				if (!$projectCreateResponse->isSuccessfull()) {
					$msg = 'Cannot create projects. Unable to continue';
					$this->loggerSrv->addError($projectCreateResponse->getErrorMessage());
					$this->monitorLogSrv->appendError([
						'message' => $msg,
					]);
				}
				$project = $projectCreateResponse->getProject();
				$xtrfRequest['projectId'] = $project->id;
				$xtrfRequests[$index] = $xtrfRequest;
			}

			$context['xtrfRequests'] = $xtrfRequests;
			$wf = $this->registry->get($history, 'wf_bl_xtrf');

			if ($wf->can($history, 'prepare')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'prepare');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			$this->loggerSrv->addError('Error in Creating Projects step for BL-XTRF workflow.', $thr);
			throw $thr;
		}
	}

	private function addLineLog(Task $task, $entityId, $isCharge = false)
	{
		$this->monitorLogSrv->appendError([
			'id' => $task->getCustomerInvoice()?->getId(),
			'number' => $task->getCustomerInvoice()?->getFinalNumber(),
			'message' => 'Unable to create line for given data.',
			'data' => [
				'task' => $task->getId(),
				'target_entity' => $entityId,
				'is_charge' => $isCharge,
				'is_cat_charge' => !$isCharge,
			],
		]);
	}

	private function addDtoLog(CustomerInvoice $ci)
	{
		$this->monitorLogSrv->appendError([
			'id' => $ci->getId(),
			'number' => $ci->getFinalNumber(),
			'message' => 'Unable to create invoice dto due lack of lines. No charges or catCharges found.',
		]);
	}
}
