<?php

namespace App\Workflow\Subscribers\XtrfQbo;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFHistory;
use App\Model\Repository\CustomerInvoiceRepository;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;

class CollectInfo implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private CustomerInvoiceRepository $customerInvoiceRepo;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		CustomerInvoiceRepository $customerInvoiceRepo,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->customerInvoiceRepo = $customerInvoiceRepo;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_QBO);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.wf_xtrf_qbo.completed.start' => 'collectInfo',
		];
	}

	public function collectInfo(Event $event)
	{
		$this->loggerSrv->addInfo('Starting collect DB invoices from filters');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		/** @var AVWorkflowMonitor $monitorObj */
		$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
		if ($monitorObj) {
			$this->monitorLogSrv->setMonitor($monitorObj);
		}
		$filters = $context['filters'];
		unset($context['filters']);
		$wf = $this->registry->get($history, 'wf_xtrf_qbo');

		if (empty($filters)) {
			$msg = 'No filters was found. Unable to continue.';
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		try {
			if (!$monitorObj->getAuxiliaryData()) {
				$dbInvoiceListObj = $this->customerInvoiceRepo->getSearchInvoicesIds($filters);
			} else {
				$dbInvoiceListObj = $monitorObj->getAuxiliaryData();
			}
			if (!$dbInvoiceListObj) {
				$msg = 'There is not invoices with provided filters. Unable to continue.';
				if (!empty($context['monitor_id'])) {
					$msg .= "Monitor id {$context['monitor_id']}";
				}
				$this->monitorLogSrv->appendError([
					'id' => $context['monitor_id'] ?? 'undefined',
					'message' => $msg,
				]);
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			$dbInvoiceList = [array_shift($dbInvoiceListObj)];
			$this->monitorLogSrv->getMonitor()->setAuxiliaryData($dbInvoiceListObj);
			$this->em->persist($this->monitorLogSrv->getMonitor());
			$this->em->flush();

			$context['dbInvoices'] = $dbInvoiceList;

			if ($wf->can($history, 'collect')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'collect');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			$this->loggerSrv->addError('Error in CollectInfo step for XTRF-QBO workflow.', $thr);
			throw $thr;
		}
	}
}
