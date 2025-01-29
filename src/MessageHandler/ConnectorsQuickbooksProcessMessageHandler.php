<?php

namespace App\MessageHandler;

use App\Linker\Services\QboService;
use App\Message\ConnectorsQuickbooksProcessMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConnectorsQuickbooksProcessMessageHandler
{
	public const ENTITY_NAME_CUSTOMER = 'Customer';
	public const ENTITY_NAME_ACCOUNT = 'Account';
	public const ENTITY_NAME_BILL = 'Bill';
	public const ENTITY_NAME_INVOICE = 'Invoice';
	public const ENTITY_NAME_VENDOR = 'Vendor';
	public const ENTITY_NAME_ITEM = 'Item';
	public const ENTITY_NAME_CUSTOMER_PAYMENT = 'Payment';
	public const ENTITY_NAME_BILL_PAYMENT = 'BillPayment';
	private QboService $qboService;
	private LoggerService $loggerSrv;

	public function __construct(
		QboService $qboService,
		LoggerService $loggerSrv,
	) {
		$this->qboService = $qboService;
		$this->loggerSrv = $loggerSrv;
	}

	public function __invoke(ConnectorsQuickbooksProcessMessage $message): void
	{
		$dataProcess = $message->getData();
		$this->loggerSrv->addInfo('Entering to process QBO');
		do {
			if (null === $dataProcess) {
				$this->loggerSrv->addWarning('Qbo process is empty.');
				break;
			}
			$payload = json_decode($dataProcess, true);
			if (isset($payload['eventNotifications'])) {
				$payload = array_shift($payload['eventNotifications']);
				$entities = $payload['dataChangeEvent']['entities'] ?? [];
				foreach ($entities as $entity) {
					switch ($entity['name']) {
						case self::ENTITY_NAME_CUSTOMER:
							$this->qboService->processCustomer($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_ACCOUNT:
							$this->qboService->processAccount($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_BILL:
							$this->qboService->processBill($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_INVOICE:
							$this->qboService->processInvoice($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_VENDOR:
							$this->qboService->processQboProvider($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_ITEM:
							$this->qboService->processQboItem($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_CUSTOMER_PAYMENT:
							$this->qboService->processQboCustomerPayment($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_BILL_PAYMENT:
							$this->qboService->processQboProviderPayment($entity['id'], $entity['operation']);
							break;
					}
				}
			}
		} while (0);
	}
}
