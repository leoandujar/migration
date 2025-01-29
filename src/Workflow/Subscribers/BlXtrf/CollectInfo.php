<?php

namespace App\Workflow\Subscribers\BlXtrf;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\BlCustomer;
use App\Model\Entity\WFHistory;
use App\Model\Repository\BlCallRepository;
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
	private BlCallRepository $blCallRepository;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		BlCallRepository $blCallRepository,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->blCallRepository = $blCallRepository;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_BL_XTRF);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.wf_bl_xtrf.completed.start' => 'collectInfo',
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
		$template = $context['template'];
		unset($context['filters']);
		$wf = $this->registry->get($history, 'wf_bl_xtrf');

		if (empty($filters)) {
			$msg = 'No filters was found. Unable to continue.';
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		try {
			$blCustomers = $filters['customer_id'];
			if (!$blCustomers) {
				$msg = 'There is no customers with provided filters. Unable to continue.';
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

			$xtrfRequests = [];
			foreach ($blCustomers as $blCustomerId) {
				$blCustomer = $this->em->getRepository(BlCustomer::class)->find($blCustomerId);
				if ($blCustomer) {
					$calls = $this->blCallRepository->getCallsByCustomer($blCustomerId, $filters);
					$targetLangIds = array_values(array_unique(array_column($calls, 'languageId')));
					$macroParams = [
						'languages' => [],
					];
					$projectRequest = [
						'customerId' => $blCustomer->getCustomer()->getId(),
						'serviceId' => $template['service'],
						'specializationId' => $template['specialization'],
						'sourceLanguageId' => $template['source_language'],
						'targetLanguagesIds' => $targetLangIds,
					];

					foreach ($calls as $call) {
						if (!empty($call['languageId'])) {
							$call['date'] = $call['date']->format('Y-m-d H:i');
							$macroParams['languages'][$call['languageId']]['payable'][] = [
								'minutes' => floatval($call['duration']),
								'rate' => $call['duration'] > 0 ? $call['blAmount'] / $call['duration'] : 0,

								'description' => json_encode($call),
							];
							$macroParams['languages'][$call['languageId']]['receivable'][] = [
								'minutes' => floatval($call['duration']),
								'rate' => $call['duration'] > 0 ? $call['amount'] / $call['duration'] : 0,
								'description' => json_encode($call),
							];
						}
					}

					$xtrfRequests[] = [
						'projectRequests' => $projectRequest,
						'macroParams' => $macroParams,
					];
				}
			}

			$context['xtrfRequests'] = $xtrfRequests;

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
			$this->loggerSrv->addError('Error in CollectInfo step for BL-XTRF workflow.', $thr);
			throw $thr;
		}
	}
}
