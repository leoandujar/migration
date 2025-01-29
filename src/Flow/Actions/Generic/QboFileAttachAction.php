<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\Qbo\QboConnector;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use QuickBooksOnline\API\Core\HttpClients\FaultHandler;
use QuickBooksOnline\API\Data\IPPAttachable;
use QuickBooksOnline\API\Data\IPPAttachableRef;
use QuickBooksOnline\API\Data\IPPReferenceType;

class QboFileAttachAction extends Action
{
	public const ACTION_DESCRIPTION = 'Attach PDFs to QBO invoices';
	public const ACTION_INPUTS = [
		'dtoList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
            'description' => 'List of DTOs with the path of the PDFs to be attached to the invoices.',
		],
	];

	public const ACTION_OUTPUTS = [
		'pdfsAttachError' => [
			'description' => 'List of PDFs that could not be attached.',
			'type' => 'array',
		],
		'correctlyAttached' => [
			'description' => 'List of PDFs that were correctly attached.',
			'type' => 'array',
		],
	];
	private QboConnector $qboCon;
	private CloudFileSystemService $fileBucketService;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		QboConnector $qboCon,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->qboCon = $qboCon;
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'QboFileAttachAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$dtoList = $this->aux['dtoList'];

		$pdfsAttachError = [];
		$correctlyAttached = [];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_INVOICES);

			foreach ($dtoList as $item) {
				if (self::PROCESS_STATUS_FAILURE === $item['status']) {
					continue;
				}

				$qboDto = $item['dto'];
				$qboResponse = $item['qboResponse'];

				$pdfContent = $this->fileBucketService->download($item['path']);

				if (empty($pdfContent)) {
					$message = "[FLOW]: Unable to attach PDF in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. Invoice ID: $qboResponse->Id. PDF not found.";
					$this->sendErrorMessage(
						$message,
						[
							'qboId' => $qboResponse->Id,
							'invoiceId' => $qboDto->docNumber,
							'status' => 'Failure',
							'errorType' => 'Pdf empty',
							'message' => 'PDF not found',
						],
						null,
						null
					);

					$pdfsAttachError[] = [
						'message' => 'PDF not found',
						'status' => self::PROCESS_STATUS_FAILURE,
						'date' => (new \DateTime())->format('Y-m-d'),
						'title' => $message,
					];
					continue;
				}

				$mymeType = $this->fileBucketService->mimeType($item['path']);
				$file = [
					'name' => $qboDto->docNumber.'.pdf',
					'contents' => base64_encode($pdfContent),
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
					$this->sendErrorMessage(
						"[FLOW]: Unable to attach PDF in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. {$qboAttachmentResponse->getIntuitErrorDetail()}",
						[
							'invoiceId' => $qboDto->docNumber,
							'qboId' => $qboResponse->Id,
							'status' => 'Failure',
							'errorType' => 'Attachment not created',
							'number' => $qboDto->docNumber,
							'message' => $qboAttachmentResponse->getIntuitErrorDetail(),
						],
						null,
						null
					);

					$pdfsAttachError[] = [
						'message' => $qboAttachmentResponse->getIntuitErrorDetail(),
						'status' => self::PROCESS_STATUS_FAILURE,
						'date' => (new \DateTime())->format('Y-m-d'),
						'title' => "[FLOW]: Unable to attach PDF in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber",
					];
					continue;
				}

				$this->loggerSrv->addInfo("[FLOW]: Successfully created invoice and attachment in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. Invoice ID: $qboResponse->Id.");

				$this->sendSuccess(
					[
						'qboId' => $qboResponse->Id,
						'invoiceId' => $qboDto->docNumber,
						'status' => 'Success',
						'message' => 'PDF attached successfully',
						'data' => [
							'invoice_dto' => $qboDto,
							'attachment' => $qboAttachmentResponse,
						],
					]
				);

				$correctlyAttached[] = [
					'message' => 'PDF attached successfully',
					'status' => self::PROCESS_STATUS_SUCCESS,
					'date' => (new \DateTime())->format('Y-m-d'),
					'title' => "[FLOW]: Successfully created invoice and attachment in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. Invoice ID: $qboResponse->Id.",
				];
			}

			$this->outputs = [
				'pdfsAttachError' => $pdfsAttachError,
				'correctlyAttached' => $correctlyAttached,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
