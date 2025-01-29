<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\Qbo\QboConnector;
use App\Model\Entity\CustomerInvoice;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use QuickBooksOnline\API\Core\HttpClients\FaultHandler;

class QboInvoiceCreateAction extends Action
{
	public const ACTION_DESCRIPTION = 'Creates invoices in QBO';
	public const ACTION_INPUTS = [
		'dtoList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of DTOs with the data to create the invoices in QBO.',
		],
	];

	public const ACTION_OUTPUTS = [
		'dtoList' => [
			'description' => 'List of invoices with the status of the process.',
			'type' => 'array',
		],
		'invoicesError' => [
			'description' => 'List of invoices that could not be created.',
			'type' => 'array',
		],
		'ciId' => [
			'description' => 'Customer Invoice ID.',
			'type' => 'integer',
		],
	];
	private QboConnector $qboCon;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		QboConnector $qboCon,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->qboCon = $qboCon;
		$this->actionName = 'QboInvoiceCreateAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$dtoList = $this->aux['dtoList'];

		$this->sendStartMessage();

		$invoicesError = [];

		try {
			$this->setMonitorObject();

			foreach ($dtoList as $index => $item) {
				$qboDto = $item['dto'];
				$qboResponse = $this->qboCon->createInvoice($qboDto->toArray());
				if (false !== $qboResponse && $qboResponse instanceof FaultHandler) {
					$message = "[FLOW]: Unable to create invoice in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. {$qboResponse->getIntuitErrorDetail()}";
					$invoicesError[] = [
						'message' => $qboResponse->getIntuitErrorDetail(),
						'status' => self::PROCESS_STATUS_FAILURE,
						'date' => (new \DateTime())->format('Y-m-d'),
						'title' => $message,
					];
					$dtoList[$index]['status'] = self::PROCESS_STATUS_FAILURE;
					$this->sendErrorMessage(
						null,
						[
							'qboId' => $qboResponse->getIntuitErrorCode(),
							'message' => $qboResponse->getIntuitErrorDetail(),
							'status' => 'Failure',
							'invoiceId' => $qboDto->docNumber,
							'total' => $qboDto->totalAmount,
							'errorType' => 'Invoice not created',
						],
						null,
						null
					);
					continue;
				}
				if (empty($qboResponse->Id)) {
					$message = "[FLOW]: Unable to create invoice in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. Invoice ID not returned.";
					$invoicesError[] = [
						'message' => 'Connection with QBO closed or session expired',
						'status' => self::PROCESS_STATUS_FAILURE,
						'date' => (new \DateTime())->format('Y-m-d'),
						'title' => $message,
					];
					$dtoList[$index]['status'] = self::PROCESS_STATUS_FAILURE;
					$this->sendErrorMessage(
						null,
						[
							'message' => $message,
							'status' => 'Failure',
							'invoiceId' => $qboDto->docNumber,
							'total' => $qboDto->totalAmount,
							'errorType' => 'Invoice not created',
						],
						null,
						null
					);
					continue;
				}
				$ci = $this->em->getRepository(CustomerInvoice::class)->findOneBy(['finalNumber' => $qboDto->docNumber]);
				if ($ci) {
					$ci->setQboId($qboResponse->Id);
					$this->em->persist($ci);
					$this->em->flush();
				}
				$message = "[FLOW]: Successfully created invoice in QBO for monitor ID $this->monitorId and invoice# $qboDto->docNumber. Invoice ID: $qboResponse->Id";
				$this->loggerSrv->addInfo($message);
				$this->sendSuccess(
					[
						'customerId' => $ci->getId() ?? null,
						'qboId' => $qboResponse->Id,
						'invoiceId' => $qboDto->docNumber,
						'message' => $message,
						'status' => 'Success',
						'total' => $qboDto->totalAmount,
						'data' => [
							'invoice_dto' => $qboDto,
						],
					]
				);
				$dtoList[$index]['status'] = 'success';
				$dtoList[$index]['qboResponse'] = $qboResponse;
			}

			$this->outputs = [
				'dtoList' => $dtoList,
				'invoicesError' => $invoicesError ?? [],
				'ciId' => isset($ci) ? $ci->getId() : 'without id',
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), $thr->getMessage());

			return self::ACTION_STATUS_ERROR;
		}
	}
}
