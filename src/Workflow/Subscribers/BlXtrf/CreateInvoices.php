<?php

namespace App\Workflow\Subscribers\BlXtrf;

use App\Connector\Xtrf\XtrfConnector;
use App\Connector\XtrfMacro\MacroConnector;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFHistory;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;

class CreateInvoices implements EventSubscriberInterface
{
	private Registry $registry;
	private XtrfConnector $xtrfConnector;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private MacroConnector $macroConn;

	public function __construct(
		Registry $registry,
		XtrfConnector $xtrfConnector,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		MacroConnector $macroConn,
	) {
		$this->em = $em;
		$this->xtrfConnector = $xtrfConnector;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->macroConn = $macroConn;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_BL_XTRF);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.wf_bl_xtrf.completed.prepare' => 'createInvoicing',
		];
	}

	public function createInvoicing(Event $event)
	{
		$this->loggerSrv->addInfo('Starting creating invoices in XTRF');
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
		$macro = $context['macro'];
		try {
			foreach ($xtrfRequests as $xtrfRequest) {
				$projectId = $xtrfRequest['projectId'];
				$macroResponse = $this->macroConn->runMacro(
					$macro,
					[$projectId],
					$xtrfRequest['macroParams'],
					false,
				);
				if (!$macroResponse->isSuccessfull()) {
					$monitorId = $this->monitorLogSrv->getMonitor()?->getId();
					$this->loggerSrv->addCritical("Unable to create invoice in XTRF for monitor ID $monitorId and project $projectId. {$macroResponse->getErrorMessage()}");
					$this->monitorLogSrv->appendError([
						'id' => $projectId,
						'number' => $projectId,
						'message' => $macroResponse->getErrorMessage(),
					]);
					continue;
				}
				if ($macroResponse->url) {
					$macroResult = file_get_contents($macroResponse->url);

					$jsonObject = json_decode($macroResult);
				}
				$this->monitorLogSrv->appendSuccess([
					'id' => $projectId,
					'number' => $projectId,
					'data' => $jsonObject,
				]);
			}

			$wf = $this->registry->get($history, 'wf_bl_xtrf');
			if ($wf->can($history, 'invoicing')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'invoicing');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			$this->loggerSrv->addError('Error in CollectInfo step for BL-XTRF workflow.', $thr);
			throw $thr;
		}
	}
}
