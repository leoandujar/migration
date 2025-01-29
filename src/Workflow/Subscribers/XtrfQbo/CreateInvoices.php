<?php

namespace App\Workflow\Subscribers\XtrfQbo;

use App\Connector\Qbo\QboConnector;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFHistory;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use QuickBooksOnline\API\Core\HttpClients\FaultHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use QuickBooksOnline\API\Data\IPPReferenceType;
use QuickBooksOnline\API\Data\IPPAttachableRef;
use QuickBooksOnline\API\Data\IPPAttachable;

class CreateInvoices implements EventSubscriberInterface
{
	private Registry $registry;
	private QboConnector $qboCon;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private CloudFileSystemService $fileBucketService;

	public function __construct(
		Registry $registry,
		QboConnector $qboCon,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		CloudFileSystemService $fileBucketService,
	) {
		$this->em = $em;
		$this->qboCon = $qboCon;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_QBO);
		$this->fileBucketService = $fileBucketService;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.wf_xtrf_qbo.completed.prepare' => 'createInvoicing',
		];
	}

	public function createInvoicing(Event $event)
	{
		$this->loggerSrv->addInfo('Starting creating invoices on QBO');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		if ($context['monitor_id']) {
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
			$monitorId = $this->monitorLogSrv->getMonitor()?->getId();
		}
		$dtoList = $context['dtoList'];
		unset($context['dtoList']);

		try {
			foreach ($dtoList as $item) {
				$qboDto = $item['dto'];
				$qboResponse = $this->qboCon->createInvoice($qboDto->toArray());
				if (false !== $qboResponse && $qboResponse instanceof FaultHandler) {
					$this->loggerSrv->addError("Unable to create invoice in QBO for monitor ID $monitorId and invoice# $qboDto->docNumber. {$qboResponse->getIntuitErrorDetail()}");
					$this->monitorLogSrv->appendError([
						'id' => $qboDto->docNumber,
						'number' => $qboDto->docNumber,
						'message' => $qboResponse->getIntuitErrorDetail(),
					]);
					continue;
				}
				if (empty($qboResponse->Id)) {
					$this->loggerSrv->addError("Unable to create invoice in QBO for monitor ID $monitorId and invoice# $qboDto->docNumber. Invoice ID not returned.");
					$this->monitorLogSrv->appendError([
						'id' => $qboDto->docNumber,
						'number' => $qboDto->docNumber,
						'message' => 'Connection with QBO closed or session expired',
					]);
					continue;
				}
				$this->loggerSrv->addInfo("Attaching PDF in QBO for monitor ID $monitorId and invoice# $qboDto->docNumber. Invoice ID: $qboResponse->Id");
				$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_INVOICES);
				$pdfContent = $this->fileBucketService->download($item['path']);
				if (empty($pdfContent)) {
					$this->loggerSrv->addError("Unable to attach PDF in QBO for monitor ID $monitorId and invoice# $qboDto->docNumber. Invoice ID: $qboResponse->Id. PDF not found.");
					$this->monitorLogSrv->appendError([
						'id' => $qboResponse->Id,
						'number' => $qboDto->docNumber,
						'message' => 'PDF not found',
					]);
					continue;
				}

				$mymeType = $this->fileBucketService->mimeType($item['path']);
				$file = [
					'name' => $qboDto->docNumber.'.pdf',
					'contents' => $pdfContent,
					'mimeType' => $mymeType,
				];

				$entityRef = new IPPReferenceType(['value' => $qboResponse->Id, 'type' => 'Invoice']);
				$attachableRef = new IPPAttachableRef(['EntityRef' => $entityRef]);
				$objAttachable = new IPPAttachable();
				$objAttachable->FileName = $file['name'];
				$objAttachable->AttachableRef = $attachableRef;
				$objAttachable->Category = 'Pdf';
				$qboAttachmentResponse = $this->qboCon->createAttachment($file, $objAttachable);
				if (false !== $qboAttachmentResponse && $qboAttachmentResponse instanceof FaultHandler) {
					$this->loggerSrv->addError("Unable to attach PDF in QBO for monitor ID $monitorId and invoice# $qboDto->docNumber. {$qboAttachmentResponse->getIntuitErrorDetail()}");
					$this->monitorLogSrv->appendError([
						'id' => $qboDto->docNumber,
						'number' => $qboDto->docNumber,
						'message' => $qboAttachmentResponse->getIntuitErrorDetail(),
					]);
					continue;
				}
				$this->loggerSrv->addInfo("Successfully created invoice and attachment in QBO for monitor ID $monitorId and invoice# $qboDto->docNumber.");
				$this->monitorLogSrv->appendSuccess([
					'id' => $qboResponse->Id,
					'number' => $qboDto->docNumber,
					'data' => [
						'invoice_dto' => $qboDto,
						'attachment' => $qboAttachmentResponse,
					],
				]);
			}

			$wf = $this->registry->get($history, 'wf_xtrf_qbo');
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
			$this->loggerSrv->addError('Error in CollectInfo step for XTRF-QBO workflow.', $thr);
			throw $thr;
		}
	}
}
