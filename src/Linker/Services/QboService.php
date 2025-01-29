<?php

namespace App\Linker\Services;

use App\Message\ConnectorsQuickbooksProcessMessage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use App\Service\LoggerService;
use App\Model\Entity\QboItem;
use App\Model\Entity\Customer;
use App\Model\Entity\Provider;
use App\Model\Entity\QboAccount;
use App\Model\Entity\QboProvider;
use App\Connector\Qbo\QboConnector;
use App\Model\Entity\QboPaymentItem;
use App\Model\Entity\CustomerInvoice;
use QuickBooksOnline\API\Data\IPPBill;
use QuickBooksOnline\API\Data\IPPItem;
use QuickBooksOnline\API\Data\IPPLine;
use App\Model\Entity\QboCustomerPayment;
use App\Model\Entity\QboProviderInvoice;
use App\Model\Entity\QboProviderPayment;
use Doctrine\ORM\EntityManagerInterface;
use QuickBooksOnline\API\Data\IPPVendor;
use QuickBooksOnline\API\Data\IPPAccount;
use QuickBooksOnline\API\Data\IPPInvoice;
use QuickBooksOnline\API\Data\IPPPayment;
use QuickBooksOnline\API\Data\IPPCustomer;
use QuickBooksOnline\API\Data\IPPLinkedTxn;
use App\Model\Entity\QboCustomerInvoiceItem;
use App\Model\Repository\CustomerRepository;
use QuickBooksOnline\API\Data\IPPBillPayment;
use QuickBooksOnline\API\Data\IPPEmailAddress;
use QuickBooksOnline\API\Data\IPPIntuitAnyType;
use QuickBooksOnline\API\Data\IPPWebSiteAddress;
use QuickBooksOnline\API\Data\IPPPhysicalAddress;
use QuickBooksOnline\API\Data\IPPTelephoneNumber;
use QuickBooksOnline\API\Data\IPPBillPaymentCheck;
use App\Model\Repository\CustomerInvoiceRepository;
use QuickBooksOnline\API\Data\IPPSalesItemLineDetail;
use QuickBooksOnline\API\Data\IPPModificationMetaData;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QboService
{
	public const OPERATION_CREATE = 'Create';
	private const OPERATION_UPDATE = 'Update';

	private const LINE_TYPE_PROVIDER = 'Bill';
	private const LINE_TYPE_CUSTOMER = 'Invoice';

	public const ENTITY_NAME_CUSTOMER = 'Customer';
	public const ENTITY_NAME_ACCOUNT = 'Account';
	public const ENTITY_NAME_BILL = 'Bill';
	public const ENTITY_NAME_INVOICE = 'Invoice';
	public const ENTITY_NAME_VENDOR = 'Vendor';
	public const ENTITY_NAME_ITEM = 'Item';
	public const ENTITY_NAME_CUSTOMER_PAYMENT = 'Payment';
	public const ENTITY_NAME_BILL_PAYMENT = 'BillPayment';

	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private ParameterBagInterface $parameterBag;
	private QboConnector $qboConnector;
	private CustomerInvoiceRepository $customerInvoiceRepository;
	private CustomerRepository $customerRepository;
	private MessageBusInterface $bus;

	public function __construct(
		EntityManagerInterface $em,
		QboConnector $qboConnector,
		CustomerRepository $customerRepository,
		ParameterBagInterface $parameterBag,
		CustomerInvoiceRepository $customerInvoiceRepository,
		LoggerService $loggerSrv,
		MessageBusInterface $bus,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->parameterBag = $parameterBag;
		$this->qboConnector = $qboConnector;
		$this->customerInvoiceRepository = $customerInvoiceRepository;
		$this->customerRepository = $customerRepository;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
		$this->bus = $bus;
	}

	public function processWebhook(string $payload, $headerSignature): void
	{
		$webhookToken = $this->parameterBag->get('qbo.webhook_notification_token');
		$payloadHash = hash_hmac('sha256', $payload, $webhookToken);
		$singatureHash = bin2hex(base64_decode($headerSignature));
		if ($payloadHash !== $singatureHash) {
			$this->loggerSrv->addCritical('Request to Qbo callback was made with wrong signature.');

			return;
		}
		$this->dispatchWebhook($payload);
	}

	public function processQueueWebhook(int $totalToProcess): void
	{
		while ($totalToProcess-- > 0) {
			$payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_QBO_QUEUE);
			if (null === $payload) {
				$this->loggerSrv->addWarning('Qbo queue is empty.');
				$totalToProcess = 0;
			}

			$payload = json_decode($payload, true);
			if (isset($payload['eventNotifications'])) {
				$payload = array_shift($payload['eventNotifications']);
				$entities = $payload['dataChangeEvent']['entities'] ?? [];
				foreach ($entities as $entity) {
					switch ($entity['name']) {
						case self::ENTITY_NAME_CUSTOMER:
							$this->processCustomer($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_ACCOUNT:
							$this->processAccount($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_BILL:
							$this->processBill($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_INVOICE:
							$this->processInvoice($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_VENDOR:
							$this->processQboProvider($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_ITEM:
							$this->processQboItem($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_CUSTOMER_PAYMENT:
							$this->processQboCustomerPayment($entity['id'], $entity['operation']);
							break;
						case self::ENTITY_NAME_BILL_PAYMENT:
							$this->processQboProviderPayment($entity['id'], $entity['operation']);
							break;
					}
				}
			}
		}
	}

	private function dispatchWebhook($payload): void
	{
		try {
			$this->bus->dispatch(new ConnectorsQuickbooksProcessMessage(data:$payload));
		} catch (\Throwable $tr) {
			$this->loggerSrv->addError("Unable to send the payload for Qbo=> $payload");
			$this->loggerSrv->addError($tr->getMessage());
		}
	}

	public function processCustomer(string $id, string $operation): ?Customer
	{
		if (self::OPERATION_CREATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Customer was calling with operation different from CREATE');

			return null;
		}

		/** @var IPPCustomer $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_CUSTOMER, $id);

		if (!is_object($response)) {
			$this->loggerSrv->addWarning('qboConnector findById for entity Account return non object.');

			return null;
		}

		$qboName = $response->DisplayName;
		if (empty($qboName)) {
			$this->loggerSrv->addWarning("QBO Customer did not have value in displayName for id $response->Id");

			return null;
		}

		$qboCustomerId = $response->Id;

		/** @var Customer $customer */
		$customer = $this->customerRepository->findByName($qboName);
		if (!$customer) {
			$this->loggerSrv->addWarning("Customer from Qbo with Name $qboName was not found in DB.");

			return null;
		}
		$customer->setQboId($qboCustomerId);
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		$this->em->persist($customer);
		$this->em->flush();

		return $customer;
	}

	public function processAccount(string $id, string $operation): mixed
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Account was calling with operation different from CREATE or UPDATE');

			return null;
		}

		/** @var IPPAccount $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_ACCOUNT, $id);

		if (!is_object($response)) {
			$this->loggerSrv->addWarning('qboConnector findById for entity Account return non object.');

			return null;
		}

		$entity = $this->em->getRepository(QboAccount::class)->find($response->Id);
		if (self::OPERATION_CREATE === $operation) {
			if (null === $entity) {
				$account = new QboAccount();
				$account->setId($response->Id);
			} else {
				return null;
			}
		}
		$account
			->setName($response->Name)
			->setSubAccount($response->SubAccount)
			->setParentRef($response->ParentRef)
			->setFullyQualifiedName($response->FullyQualifiedName)
			->setActive($response->Active)
			->setClassification($response->Classification)
			->setAccountType($response->AccountType)
			->setAccountSubType($response->AccountSubType)
			->setCurrentBalance($response->CurrentBalance)
			->setCurrentBalanceWithSubAccounts($response->CurrentBalanceWithSubAccounts)
			->setCurrencyRef($response->CurrencyRef);

		/** @var IPPModificationMetaData $metadata */
		$metadata = $response->MetaData;
		if (!empty($metadata->CreatedByRef)) {
			$account->setMetadataCreatedByRef(new \DateTime("$metadata->CreatedByRef"));
		}
		if (!empty($metadata->CreateTime)) {
			$account->setMetadataCreateTime(new \DateTime("$metadata->CreateTime"));
		}
		if (!empty($metadata->LastModifiedByRef)) {
			$account->setMetadataLastModifiedByRef(new \DateTime("$metadata->LastModifiedByRef"));
		}
		if (!empty($metadata->LastUpdatedTime)) {
			$account->setMetadataLastUpdatedTime(new \DateTime("$metadata->LastUpdatedTime"));
		}
		if (!empty($metadata->LastChangedInQB)) {
			$account->setMetadataLastChangedInQB(new \DateTime("$metadata->LastChangedInQB"));
		}
		$account->setMetadataSynchronized($metadata->Synchronized);
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		$this->em->persist($account);
		$this->em->flush();

		return $account;
	}

	public function processInvoice(string $id, string $operation): ?CustomerInvoice
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('Customer Invoice was calling with operation different from CREATE or UPDATE');

			return null;
		}

		/** @var IPPInvoice $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_INVOICE, $id);

		$customerInvoice = $this->customerInvoiceRepository->findOneBy(['finalNumber' => $response->DocNumber]);

		if (!$customerInvoice) {
			$this->loggerSrv->addCritical("Unable to find Customer Invoice with ID $response->Id in XTRF Database into FINAL NUMBER");

			return null;
		}

		$customerInvoice
			->setQboId($response->Id)
			->setDeposit($response->Deposit)
			->setBalance($response->Balance);
		$this->em->persist($customerInvoice);

		if (!is_array($response->Line) && $response->Line instanceof IPPLine) {
			$response->Line = [$response->Line];
		}

		/** @var IPPLine $line */
		foreach ($response->Line as $line) {
			if (!empty($line->Id)) {
				$lineObj = new QboCustomerInvoiceItem();
				$lineObj
					->setRemoteId($line->Id)
					->setDetailType($line->DetailType)
					->setAmount($line->Amount)
					->setLineNum($line->LineNum)
					->setDescription($line->Description)
					->setCustomerInvoice($customerInvoice);
				/** @var IPPSalesItemLineDetail $salesDetails */
				$salesDetails = $line->SalesItemLineDetail;
				if (null !== $salesDetails) {
					$lineObj
						->setDiscountRate($salesDetails->DiscountRate)
						->setDiscountAmt($salesDetails->DiscountAmt)
						->setItemRef($salesDetails->ItemRef)
						->setUnitPrice($salesDetails->UnitPrice)
						->setQty($salesDetails->Qty)
						->setItemAccountRef($salesDetails->ItemAccountRef);
				}
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($lineObj);
			}
		}
		$this->em->flush();

		return $customerInvoice;
	}

	public function processQboProvider(string $id, string $operation): mixed
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Provider was calling with operation different from CREATE or UPDATE');

			return null;
		}

		/** @var IPPVendor $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_VENDOR, $id);

		/** @var IPPTelephoneNumber $extraData */
		$extraData = $response->AlternatePhone;
		if (null === $extraData) {
			$this->loggerSrv->addWarning("Extra data was NULL for QBO Id $id");
		} else {
			$this->loggerSrv->addInfo("Values for FreeFormNumber is $extraData->FreeFormNumber");
		}

		if (null !== $extraData && is_numeric($extraData->FreeFormNumber)) {
			$xtrfProviderId = $extraData->FreeFormNumber;
			/** @var Provider $xtrfProvider */
			$xtrfProvider = $this->em->getRepository(Provider::class)->find($xtrfProviderId);
			if (null === $xtrfProvider) {
				$this->loggerSrv->addWarning("XTRF provider was not found for QboProvider $xtrfProviderId");

				return null;
			}
			$xtrfProvider->setQboProvider($id);
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($xtrfProvider);
			$this->em->flush();

			return null;
		}

		$entity = $this->em->getRepository(QboProvider::class)->find($response->Id);
		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $entity) {
					return null;
				}
				$qboProvider = new QboProvider();
				$qboProvider->setId($response->Id);
				break;
			case self::OPERATION_UPDATE:
				if (null === $entity) {
					return null;
				}
				break;
		}

		$category = $extraData->FreeFormNumber ?? null;
		$qboProvider
			->setGivenName($response->GivenName)
			->setDisplayName($response->DisplayName)
			->setFamilyName($response->FamilyName)
			->setAcctNum($response->AcctNum)
			->setCompanyName($response->CompanyName)
			->setActive($response->Active)
			->setCategory($category)
			->setBalance($response->Balance);

		/** @var IPPPhysicalAddress $addressData */
		$addressData = $response->BillAddr;
		if (null !== $addressData) {
			if (!empty($addressData->City)) {
				$qboProvider->setCity($addressData->City);
			}
			if (!empty($addressData->Line1)) {
				$qboProvider->setAddress($addressData->Line1);
			}
			if (!empty($addressData->PostalCode)) {
				$qboProvider->setPostalCode($addressData->PostalCode);
			}
			if (!empty($addressData->Lat)) {
				$qboProvider->setLat($addressData->Lat);
			}
			if (!empty($addressData->Long)) {
				$qboProvider->setLat($addressData->Long);
			}
			if (!empty($addressData->CountrySubDivisionCode)) {
				$qboProvider->setState($addressData->CountrySubDivisionCode);
			}
		}

		/** @var IPPEmailAddress $emailData */
		$emailData = $response->PrimaryEmailAddr;
		if (null !== $emailData && !empty($emailData->Address)) {
			$qboProvider->setEmail($emailData->Address);
		}

		/** @var IPPTelephoneNumber $phoneData */
		$phoneData = $response->PrimaryPhone;
		if (null !== $phoneData && !empty($phoneData->FreeFormNumber)) {
			$qboProvider->setPhone($phoneData->FreeFormNumber);
		}

		/** @var IPPWebSiteAddress $webData */
		$webData = $response->WebAddr;
		if (null !== $webData && !empty($webData->URI)) {
			$qboProvider->setUri($webData->URI);
		}

		/** @var IPPModificationMetaData $metadata */
		$metadata = $response->MetaData;
		if (null !== $metadata) {
			if (!empty($metadata->CreateTime)) {
				$qboProvider->setMetadataCreateTime(new \DateTime("$metadata->CreateTime"));
			}
			if (!empty($metadata->LastUpdatedTime)) {
				$qboProvider->setMetadataLastUpdatedTime(new \DateTime("$metadata->LastUpdatedTime"));
			}
		}
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		$this->em->persist($qboProvider);
		$this->em->flush();

		return $qboProvider;
	}

	public function processBill(string $id, string $operation): mixed
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Bill was calling with operation different from CREATE or UPDATE');

			return null;
		}

		$xtrfProvider = null;
		$qboProvider = null;
		switch ($operation) {
			case self::OPERATION_CREATE:
				/** @var IPPBill $response */
				$response = $this->qboConnector->findById(self::ENTITY_NAME_BILL, $id);

				$entity = $this->em->getRepository(QboProviderInvoice::class)->find($response->Id);
				if ($entity) {
					$this->loggerSrv->addInfo("Qbo Provider Invoice with ID $response->Id already exists.");

					return null;
				}

				$qboProviderId = $response->VendorRef;

				/** @var IPPVendor $response */
				$qboProviderResponse = $this->qboConnector->findById(self::ENTITY_NAME_VENDOR, $qboProviderId);

				/** @var IPPTelephoneNumber $extraData */
				$extraData = $qboProviderResponse->AlternatePhone;
				if (null === $extraData) {
					$this->loggerSrv->addWarning("Invoice $id :: Provider $qboProviderId :: ExtraData is NULL");
				} else {
					$this->loggerSrv->addInfo("Invoice $id :: Provider $qboProviderId :: FreeFormNumber is $extraData->FreeFormNumber");
				}

				$qboProvider = $xtrfProvider = null;
				if (null !== $extraData && is_numeric($extraData->FreeFormNumber)) {
					$xtrfProviderId = $extraData->FreeFormNumber;
					/** @var Provider $xtrfProvider */
					$xtrfProvider = $this->em->getRepository(Provider::class)->find($xtrfProviderId);
				}

				if (!$xtrfProvider) {
					/** @var QboProvider $qboProvider */
					$qboProvider = $this->em->getRepository(QboProvider::class)->find($qboProviderId);
				}

				$providerInvoice = new QboProviderInvoice();
				$providerInvoice
					->setId($response->Id)
					->setXtrfProvider($xtrfProvider)
					->setQboProvider($qboProvider);
				break;
			case self::OPERATION_UPDATE:
				$providerInvoice = $this->em->getRepository(QboProviderInvoice::class)->find($id);
				break;
		}

		if ($providerInvoice) {
			$providerInvoice
				->setQboAccountId($response->APAccountRef)
				->setFinalDate(new \DateTime("$response->TxnDate"))
				->setTotalNetto($response->TotalAmt)
				->setFinalDate(new \DateTime("$response->DueDate"))
				->setBalance($response->Balance);

			/** @var IPPModificationMetaData $metadata */
			$metadata = $response->MetaData;
			if (null !== $metadata) {
				if (!empty($metadata->CreateTime)) {
					$providerInvoice->setCreatedOnDate(new \DateTime("$metadata->CreateTime"));
				}
				if (!empty($metadata->LastUpdatedTime)) {
					$providerInvoice->setLastModificationDate(new \DateTime("$metadata->LastUpdatedTime"));
				}
			}

			if (!empty($response->CurrencyRef)) {
				$providerInvoice->setCurrency($response->CurrencyRef);
			}

			if (!empty($response->DueDate)) {
				$providerInvoice->setRequiredPaymentDate(new \DateTime("$response->DueDate"));
			}

			try {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($providerInvoice);
				$this->em->flush();
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error flushing the Bill', $thr);
			}

			return $providerInvoice;
		}

		return null;
	}

	public function processQboItem(string $id, string $operation): mixed
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Item was calling with operation different from CREATE or UPDATE');

			return null;
		}

		/** @var IPPItem $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_ITEM, $id);

		$entity = $this->em->getRepository(QboItem::class)->find($response->Id);
		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $entity) {
					return null;
				}
				$qboItem = new QboItem();
				$qboItem->setId($response->Id);
				break;
			case self::OPERATION_UPDATE:
				if (null === $entity) {
					return null;
				}
				break;
		}

		$qboItem
			->setFullyQualifiedName($response->FullyQualifiedName)
			->setSku($response->Sku)
			->setName($response->Name)
			->setActive($response->Active)
			->setType($response->Type);

		/** @var IPPModificationMetaData $metadata */
		$metadata = $response->MetaData;
		if (null !== $metadata) {
			if (!empty($metadata->CreateTime)) {
				$qboItem->setMetadataCreateTime(new \DateTime("$metadata->CreateTime"));
			}
			if (!empty($metadata->LastUpdatedTime)) {
				$qboItem->setMetadataLastUpdatedTime(new \DateTime("$metadata->LastUpdatedTime"));
			}
		}
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		$this->em->persist($qboItem);
		$this->em->flush();

		return $qboItem;
	}

	public function processQboCustomerPayment(string $id, string $operation): mixed
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Customer Payment was calling with operation different from CREATE or UPDATE');

			return null;
		}

		/** @var IPPPayment $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_CUSTOMER_PAYMENT, $id);

		$qboCustomerPayment = $this->em->getRepository(QboCustomerPayment::class)->find($response->Id);
		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $qboCustomerPayment) {
					$this->loggerSrv->addWarning('Qbo Customer Payment already exits for operation create.');

					return null;
				}
				$qboCustomerPayment = new QboCustomerPayment();
				$qboCustomerPayment->setId($response->Id);
				break;
			case self::OPERATION_UPDATE:
				if (null === $qboCustomerPayment) {
					$this->loggerSrv->addWarning('Qbo Customer Payment does not already exits for update operation.');

					return null;
				}
				break;
		}

		$qboCustomerPayment
			->setCustomerId($response->CustomerRef)
			->setQboAccountId($response->DepositToAccountRef)
			->setPaymentMethodId($response->PaymentMethodRef)
			->setPaymentRefNum($response->PaymentRefNum)
			->setTotalAmount($response->TotalAmt)
			->setUnappliedAmount($response->UnappliedAmt)
			->setProcessPayment($response->ProcessPayment)
			->setTransactionDate(new \DateTime("$response->TxnDate"))
			->setCurrency($response->CurrencyRef)
			->setExchangeRate($response->ExchangeRate)
			->setPrivateNote($response->PrivateNote)
			->setTransactionType($response->TransactionLocationType);

		/** @var IPPModificationMetaData $metadata */
		$metadata = $response->MetaData;
		if (null !== $metadata) {
			if (!empty($metadata->CreateTime)) {
				$qboCustomerPayment->setMetadataCreateTime(new \DateTime("$metadata->CreateTime"));
			}
			if (!empty($metadata->LastUpdatedTime)) {
				$qboCustomerPayment->setMetadataLastUpdatedTime(new \DateTime("$metadata->LastUpdatedTime"));
			}
		}

		/** @var IPPLinkedTxn $linkedTxnDetails */
		$linkedTxnDetails = $response->LinkedTxn;
		if (null !== $linkedTxnDetails) {
			$qboCustomerPayment->setLinkedTransactionId($linkedTxnDetails->TxnId);
			$qboCustomerPayment->setLinkedTransactionType($linkedTxnDetails->TxnType);
		}
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		$this->em->persist($qboCustomerPayment);

		if (!is_array($response->Line) && $response->Line instanceof IPPLine) {
			$response->Line = [$response->Line];
		}

		/** @var IPPLine $line */
		foreach ($response->Line as $line) {
			$loggMsg = "LINE OBJECT FOR QBO Customer Payment $id=>";
			$loggData = ['Id' => '', 'DetailType' => '', 'Amount' => '', 'LineNum' => '', 'Description' => ''];
			if ($line instanceof IPPLine) {
				$loggData['Id'] = $line->Id;
				$loggData['DetailType'] = $line->DetailType;
				$loggData['Amount'] = $line->Amount;
				$loggData['LineNum'] = $line->LineNum;
				$loggData['Description'] = $line->Description;
			}
			$lineObj = new QboPaymentItem();
			$lineId = $line->Id ?? Uuid::v4()->__toString();
			$lineObj
				->setRemoteId($lineId)
				->setDetailType($line->DetailType)
				->setAmount($line->Amount)
				->setLineNum($line->LineNum)
				->setDescription($line->Description)
				->setQboCustomerPayment($qboCustomerPayment);

			/** @var IPPSalesItemLineDetail $salesDetails */
			$salesDetails = $line->SalesItemLineDetail;
			if (null !== $salesDetails) {
				$lineObj
					->setDiscountRate($salesDetails->DiscountRate)
					->setDiscountAmt($salesDetails->DiscountAmt)
					->setItemRef($salesDetails->ItemRef)
					->setUnitPrice($salesDetails->UnitPrice)
					->setQty($salesDetails->Qty)
					->setItemAccountRef($salesDetails->ItemAccountRef);

				$loggData['DiscountRate'] = $salesDetails->DiscountRate;
				$loggData['DiscountAmt'] = $salesDetails->DiscountAmt;
				$loggData['ItemRef'] = $salesDetails->ItemRef;
				$loggData['UnitPrice'] = $salesDetails->UnitPrice;
				$loggData['Qty'] = $salesDetails->Qty;
				$loggData['ItemAccountRef'] = $salesDetails->ItemAccountRef;
			}
			$this->loggerSrv->addInfo($loggMsg.PHP_EOL.json_encode($loggData));

			/** @var IPPLinkedTxn $linkedTxnDetails */
			$linkedTxnDetails = $line->LinkedTxn;
			$loggMsg = "LINKED DETAILS FOR QBO Customer Payment $id=>";
			$loggData = ['TxnId' => '', 'TxnType' => ''];
			if ($linkedTxnDetails instanceof IPPLinkedTxn) {
				$loggData['TxnId'] = $linkedTxnDetails->TxnId;
				$loggData['TxnType'] = $linkedTxnDetails->TxnType;
			}
			$this->loggerSrv->addInfo($loggMsg.PHP_EOL.json_encode($loggData));
			if (null !== $linkedTxnDetails) {
				$txnId = $linkedTxnDetails->TxnId;
				$txnType = $linkedTxnDetails->TxnType;
				$lineObj->setTransactionType($txnType);
				if (self::LINE_TYPE_CUSTOMER !== $txnType) {
					$this->loggerSrv->addWarning("Customer Payment contains line with type $txnType instead of Invoice");
				}
				if (null === $txnId) {
					$this->loggerSrv->addWarning('Customer Payment line has empty TxnId. Could not be linked.');
				} else {
					/** @var CustomerInvoice $customerInvoice */
					$customerInvoice = $this->customerInvoiceRepository->findOneBy(['qboId' => $txnId]);

					if ($customerInvoice) {
						$lineObj->setXtrfCustomerInvoice($customerInvoice);
					} else {
						/** @var IPPIntuitAnyType $linExDetails */
						$linExDetails = $line->LineEx;
						$this->loggerSrv->addInfo('SAVING DATA FROM LINE_EX DETAILS IN CUSTOMER PAYMENT=>');
					}
				}
			}
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($lineObj);
		}
		$this->em->flush();

		return $qboCustomerPayment;
	}

	public function processQboProviderPayment(string $id, string $operation): mixed
	{
		if (self::OPERATION_CREATE !== $operation && self::OPERATION_UPDATE !== $operation) {
			$this->loggerSrv->addWarning('QBO Provider Payment was calling with operation different from CREATE or UPDATE');

			return null;
		}

		/** @var IPPBillPayment $response */
		$response = $this->qboConnector->findById(self::ENTITY_NAME_BILL_PAYMENT, $id);

		$qboProviderPayment = $this->em->getRepository(QboProviderPayment::class)->find($response->Id);
		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $qboProviderPayment) {
					$this->loggerSrv->addWarning('Qbo Customer Payment already exits for operation create.');

					return null;
				}
				$qboProviderPayment = new QboProviderPayment();
				$qboProviderPayment->setId($response->Id);
				break;
			case self::OPERATION_UPDATE:
				if (null === $qboProviderPayment) {
					$this->loggerSrv->addWarning('Qbo Customer Payment already exits for operation create.');

					return null;
				}
				break;
		}

		$qboProviderPayment
			->setProviderId($response->VendorRef)
			->setPayType($response->PayType)
			->setDocNumber($response->DocNumber)
			->setTotalAmount($response->TotalAmt)
			->setTransactionDate(new \DateTime("$response->TxnDate"))
			->setCurrency($response->CurrencyRef);

		/** @var IPPBillPaymentCheck $checkPaymentData */
		$checkPaymentData = $response->CheckPayment;
		if (null !== $checkPaymentData) {
			if (!empty($checkPaymentData->BankAccountRef)) {
				$qboProviderPayment->setQboAccountId($checkPaymentData->BankAccountRef);
			}
		}

		/** @var IPPModificationMetaData $metadata */
		$metadata = $response->MetaData;
		if (null !== $metadata) {
			if (!empty($metadata->CreateTime)) {
				$qboProviderPayment->setMetadataCreateTime(new \DateTime("$metadata->CreateTime"));
			}
			if (!empty($metadata->LastUpdatedTime)) {
				$qboProviderPayment->setMetadataLastUpdatedTime(new \DateTime("$metadata->LastUpdatedTime"));
			}
		}

		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		$this->em->persist($qboProviderPayment);

		if (!is_array($response->Line) && $response->Line instanceof IPPLine) {
			$response->Line = [$response->Line];
		}

		/* @var IPPLine $line */
		if (is_array($response->Line)) {
			foreach ($response->Line as $line) {
				$loggMsg = "LINE OBJECT FOR QBO Provider Payment $id=>";
				$loggData = ['Id' => '', 'DetailType' => '', 'Amount' => '', 'LineNum' => '', 'Description' => '', 'DiscountRate' => '', 'DiscountAmt' => '', 'ItemRef' => '', 'UnitPrice' => '', 'Qty' => '', 'ItemAccountRef' => ''];
				if ($line instanceof IPPLine) {
					$loggData['Id'] = $line->Id;
					$loggData['DetailType'] = $line->DetailType;
					$loggData['Amount'] = $line->Amount;
					$loggData['LineNum'] = $line->LineNum;
					$loggData['Description'] = $line->Description;
				}
				$lineObj = new QboPaymentItem();
				$lineId = $line->Id ?? Uuid::v4()->__toString();
				$lineObj
					->setRemoteId($lineId)
					->setDetailType($line->DetailType)
					->setAmount($line->Amount)
					->setLineNum($line->LineNum)
					->setDescription($line->Description)
					->setQboProviderPayment($qboProviderPayment);
				/** @var IPPSalesItemLineDetail $salesDetails */
				$salesDetails = $line->SalesItemLineDetail;
				if (null !== $salesDetails) {
					$lineObj
						->setDiscountRate($salesDetails->DiscountRate)
						->setDiscountAmt($salesDetails->DiscountAmt)
						->setItemRef($salesDetails->ItemRef)
						->setUnitPrice($salesDetails->UnitPrice)
						->setQty($salesDetails->Qty)
						->setItemAccountRef($salesDetails->ItemAccountRef);

					$loggData['DiscountRate'] = $salesDetails->DiscountRate;
					$loggData['DiscountAmt'] = $salesDetails->DiscountAmt;
					$loggData['ItemRef'] = $salesDetails->ItemRef;
					$loggData['UnitPrice'] = $salesDetails->UnitPrice;
					$loggData['Qty'] = $salesDetails->Qty;
					$loggData['ItemAccountRef'] = $salesDetails->ItemAccountRef;
				}
				$this->loggerSrv->addInfo($loggMsg.PHP_EOL.json_encode($loggData));
				/** @var IPPLinkedTxn $linkedTxnDetails */
				$linkedTxnDetails = $line->LinkedTxn;
				$loggMsg = "LINKED DETAILS FOR QBO Provider Payment $id=>";
				$loggData = ['TxnId' => '', 'TxnType' => ''];
				if ($linkedTxnDetails instanceof IPPLinkedTxn) {
					$loggData['TxnId'] = $linkedTxnDetails->TxnId;
					$loggData['TxnType'] = $linkedTxnDetails->TxnType;
				}
				$this->loggerSrv->addInfo($loggMsg.PHP_EOL.json_encode($loggData));
				if (null !== $linkedTxnDetails) {
					$txnId = $linkedTxnDetails->TxnId;
					$txnType = $linkedTxnDetails->TxnType;
					$lineObj->setTransactionType($txnType);
					if (self::LINE_TYPE_PROVIDER !== $txnType) {
						$this->loggerSrv->addWarning('Provider Payment contains line with type Invoice instead of Bill');
					}
					if (null === $txnId) {
						$this->loggerSrv->addWarning('Provider Payment line has empty TxnId. Could not be linked.');
					} else {
						/** @var QboProviderInvoice $providerInvoice */
						$providerInvoice = $this->em->getRepository(QboProviderInvoice::class)->find($txnId);

						if ($providerInvoice) {
							$lineObj->setQboProviderInvoice($providerInvoice);
						}
					}
				}
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($lineObj);
			}
		}
		$this->em->flush();

		return $qboProviderPayment;
	}
}
