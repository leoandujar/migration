<?php

namespace App\Linker\Services;

use App\Apis\Shared\Util\UtilsService;
use App\Command\Services\BoostlingoFetchService;
use App\Message\ConnectorsBoostlingoFetchMessage;
use App\MessageHandler\ConnectorsBoostlingoFetchMessageHandler;
use App\Model\Entity\BlCall;
use App\Model\Entity\BlCommunicationType;
use App\Model\Entity\BlContact;
use App\Model\Entity\BlCustomer;
use App\Model\Entity\BlLanguage;
use App\Model\Entity\BlProviderInvoice;
use App\Model\Entity\BlRate;
use App\Model\Entity\BlServiceType;
use App\Model\Entity\BlTranslationType;
use App\Model\Entity\XtrfLanguage;
use App\Constant\Constants;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BoostlingoQueueService
{
	public const OPERATION_CREATE = 'creation';
	public const OPERATION_UPDATE = 'propertyChange';
	public const OPERATION_DELETE = 'deletion';
	public const OPERATION_CREATE_OR_UPDATE = 'create-or-update';

	public const ENTITY_NAME_CONTACT = 'contact';
	public const ENTITY_NAME_CUSTOMER = 'customer';
	public const ENTITY_NAME_CALL = 'call';
	public const ENTITY_NAME_DICTIONARY = 'dictionary';
	public const ENTITY_NAME_INVOICES = 'invoices';
	public const ENTITY_NAME_INVOICES_CALLS = 'invoices_calls';

	private const DICTIONARY_FIELD_LANGUAGE = 'languages';
	private const DICTIONARY_FIELD_SERVTYPE = 'serviceTypes';
	private const DICTIONARY_FIELD_COM_TYPE = 'communicationTypes';
	private const DICTIONARY_FIELD_TRANSTYPE = 'translationTypes';

	public const FIELDS_DICTIONARY = [
		self::DICTIONARY_FIELD_LANGUAGE,
		self::DICTIONARY_FIELD_SERVTYPE,
		self::DICTIONARY_FIELD_COM_TYPE,
		self::DICTIONARY_FIELD_TRANSTYPE, ];

	public const DEFAULT_PAGE_SIZE = 100;

	private UtilsService $utilsSrv;
	private LoggerService $loggerService;
	private EntityManagerInterface $em;
	private BoostlingoFetchService $boostlingoFetchSrv;
	private ConnectorsBoostlingoFetchMessageHandler $boostlingoFetchMessageHandler;

	private RedisClients $redisClients;
	private MessageBusInterface $bus;

	public function __construct(
		UtilsService $utilsSrv,
		EntityManagerInterface $em,
		LoggerService $loggerService,
		RedisClients $redisClients,
		MessageBusInterface $bus,
	) {
		$this->em = $em;
		$this->loggerService = $loggerService;
		$this->loggerService->setSubContext(__CLASS__);
		$this->loggerService->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
		$this->utilsSrv = $utilsSrv;
		$this->redisClients = $redisClients;
		$this->bus = $bus;
	}

	public function setBoostlingoFetchService(BoostlingoFetchService $boostlingoFetchSrv): void
	{
		$this->boostlingoFetchSrv = $boostlingoFetchSrv;
	}

	public function processEntity(object $data): bool
	{
		try {
			$response = false;
			do {
				$entityName = $data->entityName;
				$operation = $data->operation;
				$objectData = $data->data;
				if (empty($objectData)) {
					$this->loggerService->addError('Boostlingo processing found empty data saved in the queue.');
					break;
				}
				if (empty($operation)) {
					$this->loggerService->addError('Boostlingo processing found empty operation saved in the queue.');
					break;
				}

				return match ($entityName) {
					self::ENTITY_NAME_CONTACT => $this->processUsers($objectData, $operation),
					self::ENTITY_NAME_CUSTOMER => $this->processClients($objectData, $operation),
					self::ENTITY_NAME_CALL => $this->processCalls($data, $operation),
					self::ENTITY_NAME_INVOICES => $this->processInvoices($objectData, $operation),
					self::ENTITY_NAME_DICTIONARY => $this->processDictionary($objectData, $operation, $data->field),
					default => false,
				};
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerService->addError('Boostlingo => Error processing data in the Linker. Recommended enqueue again.', $thr);
		}

		return $response;
	}

	public function processDictionary(array $objectData, string $operation, string $field): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerService->addWarning('Object has empty ID, unable to continue.');

			return false;
		}
		$blLanguage = $blCommunicationType = $blTranslationTypes = $blServiceTypes = $blEntity = null;
		switch ($field) {
			case self::DICTIONARY_FIELD_LANGUAGE:
				$blLanguage = $this->em->getRepository(BlLanguage::class)->findOneBy(['blLanguageId' => $objectData['id']]);
				break;
			case self::DICTIONARY_FIELD_COM_TYPE:
				$blCommunicationType = $this->em->getRepository(BlCommunicationType::class)->findOneBy(['blCommunicationTypeId' => $objectData['id']]);
				break;
			case self::DICTIONARY_FIELD_TRANSTYPE:
				$blTranslationTypes = $this->em->getRepository(BlTranslationType::class)->findOneBy(['blTranslationTypeId' => $objectData['id']]);
				break;
			case self::DICTIONARY_FIELD_SERVTYPE:
				$blServiceTypes = $this->em->getRepository(BlServiceType::class)->findOneBy(['blServiceTypeId' => $objectData['id']]);
				break;
		}

		$exists = true;

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $blLanguage || null !== $blCommunicationType || null !== $blTranslationTypes || null !== $blServiceTypes) {
					$this->loggerService->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				break;
			case self::OPERATION_UPDATE:
				if (null === $blLanguage && null === $blCommunicationType && null === $blTranslationTypes && null === $blServiceTypes) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $blLanguage && null === $blCommunicationType && null === $blTranslationTypes && null === $blServiceTypes) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$blEntity = match ($field) {
						'languages' => $blLanguage,
						'communicationTypes' => $blCommunicationType,
						'translationTypes' => $blTranslationTypes,
						'serviceTypes' => $blServiceTypes,
					};
					if (null !== $blEntity) {
						$this->em->remove($blEntity);
						$this->em->flush();

						return true;
					}
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				switch ($field) {
					case self::DICTIONARY_FIELD_LANGUAGE:
						if (null === $blLanguage) {
							$blEntity = new BlLanguage();
							$exists = false;
						}
						break;
					case self::DICTIONARY_FIELD_COM_TYPE:
						if (null === $blCommunicationType) {
							$blEntity = new BlCommunicationType();
							$exists = false;
						}
						break;
					case self::DICTIONARY_FIELD_TRANSTYPE:
						if (null === $blTranslationTypes) {
							$blEntity = new BlTranslationType();
							$exists = false;
						}
						break;
					case self::DICTIONARY_FIELD_SERVTYPE:
						if (null === $blServiceTypes) {
							$blEntity = new BlServiceType();
							$exists = false;
						}

						break;
				}
				break;
			default:
				return false;
		}
		if (null !== $blEntity) {
			switch ($field) {
				case self::DICTIONARY_FIELD_LANGUAGE:
					$blEntity->setBlLanguageId($objectData['id'])
						->setEnabled($objectData['enabled'])
						->setEnglishName($objectData['englishName'] ?? '')
						->setName($objectData['name'] ?? '')
						->setCode($objectData['code'] ?? '');
					break;
				case self::DICTIONARY_FIELD_COM_TYPE:
					$blEntity->setBlCommunicationTypeId($objectData['id'])
						->setName($objectData['name']);
					break;
				case self::DICTIONARY_FIELD_TRANSTYPE:
					$comunicationType = $this->em->getRepository(BlCommunicationType::class)->findOneBy(['blCommunicationTypeId' => $objectData['communicationType']]);
					$blEntity->setBlTranslationTypeId($objectData['id'])
						->setIsAppointmentTranslationType($objectData['isAppointmentTranslationType'])
						->setName($objectData['name'])
						->setEnabled($objectData['enabled'])
						->setBlCommunicationType($comunicationType);
					break;
				case self::DICTIONARY_FIELD_SERVTYPE:
					$blEntity->setBlServiceTypeId($objectData['id'])
						->setEnabled($objectData['enabled'])
						->setName($objectData['name'])
						->setCode($objectData['code']);
					break;
			}

			$this->openConnection();
			if (!$exists) {
				$unitOfWork = $this->em->getUnitOfWork();
				$entities = $unitOfWork->getIdentityMap()[get_class($blEntity)] ?? [];

				foreach ($entities as $entity) {
					$this->em->detach($entity);
				}
			}
			$this->em->persist($blEntity);
			$this->em->flush();
		}

		return true;
	}

	public function processUsers(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerService->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$blContact = $this->em->getRepository(BlContact::class)->findOneBy(['blContactId' => $objectData['id']]);
		$exists = true;

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $blContact) {
					$this->loggerService->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				break;
			case self::OPERATION_UPDATE:
				if (null === $blContact) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $blContact) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($blContact);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $blContact) {
					$exists = false;
					$blContact = new BlContact();
				}
				break;
			default:
				return false;
		}

		$invitationDate = !empty($objectData['invitationDate']) ? new \DateTime($objectData['invitationDate']) : null;
		$blCustomerId = $objectData['idCustomer'] ?? null;
		if (!empty($blCustomerId)) {
			$blCustomer = $this->em->getRepository(BlCustomer::class)->findOneBy(['blCustomerId' => $blCustomerId]);
			if ($blCustomer) {
				$blContact->setBlCustomer($blCustomer);
			}
		}
		$blContact
			->setBlContactId($objectData['id'])
			->setInvitationDate($invitationDate)
			->setPin($objectData['pin'])
			->setEmail($objectData['email'])
			->setName($objectData['requiredName']);

		$this->openConnection();
		if (!$exists) {
			$unitOfWork = $this->em->getUnitOfWork();
			$entities = $unitOfWork->getIdentityMap()[get_class($blContact)] ?? [];

			foreach ($entities as $entity) {
				$this->em->detach($entity);
			}
		}
		$this->em->persist($blContact);
		$this->em->flush();

		return true;
	}

	public function processClients(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerService->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$blCustomer = $this->em->getRepository(BlCustomer::class)->findOneBy(['blCustomerId' => $objectData['id']]);
		$exists = true;

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $blCustomer) {
					$this->loggerService->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				break;
			case self::OPERATION_UPDATE:
				if (null === $blCustomer) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $blCustomer) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($blCustomer);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $blCustomer) {
					$exists = false;
					$blCustomer = new BlCustomer();
				}
				break;
			default:
				return false;
		}

		$invitedDate = !empty($objectData['invitedDate']) ? new \DateTime($objectData['invitedDate']) : null;
		$acceptedDate = !empty($objectData['acceptedDate']) ? new \DateTime($objectData['acceptedDate']) : null;

		$blCustomer
			->setBlCustomerId($objectData['id'])
			->setInvitedDate($invitedDate)
			->setAcceptedDate($acceptedDate)
			->setStatus($objectData['status'])
			->setUserNumber($objectData['numberOfUsers'] ?? 0)
			->setName($objectData['name']);

		$this->openConnection();
		if (!$exists) {
			$unitOfWork = $this->em->getUnitOfWork();
			$entities = $unitOfWork->getIdentityMap()[get_class($blCustomer)] ?? [];

			foreach ($entities as $entity) {
				$this->em->detach($entity);
			}
		}
		$this->em->persist($blCustomer);
		$this->em->flush();

		return true;
	}

	public function processCalls(object $objectRaw, string $operation): bool
	{
		$objectData = $objectRaw->data;
		if (empty($objectData['data']['id'])) {
			$this->loggerService->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$blCall = $this->em->getRepository(BlCall::class)->findOneBy(['blCallId' => $objectData['data']['id']]);
		$exists = true;

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $blCall) {
					$this->loggerService->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				break;
			case self::OPERATION_UPDATE:
				if (null === $blCall) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $blCall) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($blCall);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $blCall) {
					$exists = false;
					$blCall = new BlCall();
				}
				break;
			default:
				return false;
		}
		$properties = $objectData['data'];
		$blContact = $this->em->getRepository(BlContact::class)->findOneBy(['blContactId' => $properties['clientAccountId']]);
		if (!$blContact) {
			$this->loggerService->addWarning("Unable to find BlContact {$properties['clientAccountId']}");
		}
		$blCustomer = $this->em->getRepository(BlCustomer::class)->findOneBy(['blCustomerId' => $properties['clientCompanyId']]);
		if (!$blCustomer) {
			$this->loggerService->addWarning("Unable to find BLCustomer {$properties['clientCompanyId']}");
		}
		$blServiceType = $this->em->getRepository(BlServiceType::class)->findOneBy(['blServiceTypeId' => $properties['serviceTypeId']]);
		$blCommunicationType = $this->em->getRepository(BlCommunicationType::class)->findOneBy(['blCommunicationTypeId' => $properties['communicationTypeId']]);
		$blSourceLanguage = $this->em->getRepository(BlLanguage::class)->findOneBy(['blLanguageId' => $properties['languageFromId']]);
		$blTargetLanguage = $this->em->getRepository(BlLanguage::class)->findOneBy(['blLanguageId' => $properties['languageToId']]);

		if (!$blTargetLanguage) {
			$totalProcessed = 0;
			$this->boostlingoFetchMessageHandler->processExistingDictionaries(entityName: 'dictionaries', totalProcessed: $totalProcessed);
			try {
				$this->bus->dispatch(new ConnectorsBoostlingoFetchMessage(id:$objectData['data']['id']));
			} catch (\Throwable $tr) {
				$this->loggerService->addWarning("Error in $tr");
			}

			return false;
		}

		if (!$blTargetLanguage->getXtrfLanguage()) {
			$xtrfOtherLanguage = $this->em->getRepository(XtrfLanguage::class)->find(397);
			$blTargetLanguage->setXtrfLanguage($xtrfOtherLanguage);
			$this->em->persist($blTargetLanguage);
		}

		$startDate = !empty($properties['timeConnected']) ? new \DateTime($properties['timeConnected'], new \DateTimeZone('UTC')) : null;
		$startDate->setTimeZone(new \DateTimeZone('America/Los_Angeles'));
		$peerRatingByCustomer = (null !== $properties['peerRatingByClient']) ? intval($properties['peerRatingByClient']) : 0;
		$callQualityByCustomer = (null !== $properties['callQualityByClient']) ? intval($properties['callQualityByClient']) : 0;
		$customerAmount = (null !== $properties['invoiceAmount']) ? floatval($properties['invoiceAmount']) : 0;
		$interpreterAmount = (null !== $properties['interpreterAmount']) ? floatval($properties['interpreterAmount']) : 0;
		$duration = $this->utilsSrv->getSecondsFromPTDate($properties['duration']);
		$thirdPartyDuration = $this->utilsSrv->getSecondsFromPTDate($properties['thirdPartyDuration']);
		$operatorDuration = $this->utilsSrv->getSecondsFromPTDate($properties['operatorDuration']);
		$intakeDuration = $this->utilsSrv->getSecondsFromPTDate($properties['intakeDuration']);
		$queueDuration = $properties['queueTimeSeconds'] ? ceil($properties['queueTimeSeconds']) : 0;
		$requester = null;
		if (array_key_exists('2379_2648', $properties['customFieldsData'])) {
			$requester = sprintf(
				'%s %s',
				$properties['customFieldsData']['2379_2648'],
				$properties['customFieldsData']['2379_2649']
			);
		}

		$minimalDuration = $duration >= 30 && $duration < 120;
		$displaySeconds = ($duration % 3600) % 60;
		$durationMinutes = intdiv($duration, 60);
		$displayMinutes = $durationMinutes % 60;
		$displayHours = intdiv($durationMinutes, 60);
		$roundedDuration = ($displaySeconds > 0) ? 1 : 0;
		$customerDuration = $durationMinutes + $roundedDuration;
		$customerMinimumDuration = $minimalDuration ? 2 : $customerDuration;

		$blRateId = intval($properties['communicationTypeId'].$properties['languageFromId'].$properties['languageToId']);
		$blRate = $this->em->getRepository(BlRate::class)->findOneBy(['blRateId' => $blRateId]);
		$conference = empty($properties['otherParticipants']) ? 0 : (1 == $properties['communicationTypeId'] ? 0.5 : 1);

		$routingAmount = $interpreterAmount > 0 ? 0 : (0.09 * $customerDuration);
		$rate = $blRate ? $blRate->getRate() : 0.78;
		$blAmount = $routingAmount > 0 ? ($routingAmount + $conference) : (($customerMinimumDuration * $rate) + $conference);

		$blCall
			->setBlCallId($properties['id'])
			->setBlReferenceId($properties['accountUniqueId'])
			->setIsCrowdClient($properties['isCrowdClient'] ?? false)
			->setInterpreterName($properties['interpreterNameForInterpreter'])
			->setInterpreterReferralNumber($properties['interpreterReferralNumber'] ?? '')
			->setBlContact($blContact)
			->setCustomerName($properties['clientName'])
			->setStartDate($startDate)
			->setBlServiceType($blServiceType)
			->setBlCommunicationType($blCommunicationType)
			->setDuration($duration)
			->setCustomerDuration($customerDuration)
			->setBlSourceLanguage($blSourceLanguage)
			->setBlTargetLanguage($blTargetLanguage)
			->setPeerRatingByInterpreter($properties['peerRatingByInterpreter'])
			->setCallQualityByInterpreter($properties['callQualityByInterpreter'])
			->setPeerRatingByCustomer($peerRatingByCustomer)
			->setCallQualityByCustomer($callQualityByCustomer)
			->setCustomerAmount($customerAmount)
			->setQueueDuration($queueDuration)
			->setFromNumber($properties['fromNumber'])
			->setThirdParty($properties['otherParticipants'])
			->setThirdPartyDuration($thirdPartyDuration)
			->setOperatorDuration($operatorDuration)
			->setIntakeDuration($intakeDuration)
			->setTollFreeDialed($properties['TollFreeDialed'])
			->setInterpreterAmount($interpreterAmount)
			->setIsBackstopAnswered($properties['isBackstopAnswered'])
			->setBlCustomer($blCustomer)
			->setIsDurationUpdatePending($properties['isDurationUpdatePending'])
			->setStatus($properties['callStatus'])
			->setCustomerUniqueId($properties['clientCompanyUniqueId'])
			->setAdditional($properties['customFieldsData'])
			->setRequester($requester)
			->setBlRate($blRate)
			->setRoutingAmount($routingAmount)
			->setDurationMinimal($minimalDuration)
			->setDurationSeconds($displaySeconds)
			->setDurationMinutes($displayMinutes)
			->setDurationHours($displayHours)
			->setBlAmount($blAmount);
		$this->openConnection();
		if (!$exists) {
			$unitOfWork = $this->em->getUnitOfWork();
			$entities = $unitOfWork->getIdentityMap()[get_class($blCall)] ?? [];

			foreach ($entities as $entity) {
				$this->em->detach($entity);
			}
		}
		$this->em->persist($blCall);
		$this->em->flush();

		return true;
	}

	public function processInvoices(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerService->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$blInvoice = $this->em->getRepository(BlProviderInvoice::class)->findOneBy(['blProviderInvoiceId' => $objectData['id']]);
		$exists = true;

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $blInvoice) {
					$this->loggerService->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				break;
			case self::OPERATION_UPDATE:
				if (null === $blInvoice) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $blInvoice) {
					$this->loggerService->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($blInvoice);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $blInvoice) {
					$exists = false;
					$blInvoice = new BlProviderInvoice();
				}
				break;
			default:
				return false;
		}
		$properties = $objectData;

		$createdDate = !empty($properties['createdDate']) ? new \DateTime($properties['createdDate']) : null;
		$dueDate = !empty($properties['dueDate']) ? new \DateTime($properties['dueDate']) : null;
		$startDate = !empty($properties['startDate']) ? new \DateTime($properties['startDate']) : null;
		$endDate = !empty($properties['endDate']) ? new \DateTime($properties['endDate']) : null;
		$invoiceDate = !empty($properties['invoiceDate']) ? new \DateTime($properties['invoiceDate']) : null;

		$blInvoice
			->setBlProviderInvoiceId($properties['id'])
			->setCreatedDate($createdDate)
			->setAdminCreated($properties['adminCreated'])
			->setDueDate($dueDate)
			->setStartDate($startDate)
			->setEndDate($endDate)
			->setInvoiceDate($invoiceDate)
			->setStatus(Constants::getBoostlingProviderInvoiceStatusMap()[$properties['invoiceStateId']] ?? null)
			->setNumber($properties['invoiceNumber'] ?? null)
			->setName($properties['invoicedName'] ?? null)
			->setType($properties['invoicedType'] ?? null)
			->setNumberOfAppointments($properties['numberOfAppointments'] ?? null)
			->setNumberOfCalls($properties['numberOfCalls'] ?? null)
			->setPoNumber($properties['poNumber'] ?? null)
			->setRevisedCount($properties['revisedCount'] ?? null)
			->setInvoicedId($properties['invoicedId'] ?? null)
			->setTotal($properties['total'] ?? null)
			->setExportStateId($properties['exportStateId'] ?? null)
			->setInvoiceTermsId($properties['invoiceTermsId'] ?? null)
			->setQuickBooksId($properties['quickBooksId'] ?? null)
			->setInvoicedImageKey($properties['invoicedImageKey'] ?? null);
		$this->openConnection();
		if (!$exists) {
			$unitOfWork = $this->em->getUnitOfWork();
			$entities = $unitOfWork->getIdentityMap()[get_class($blInvoice)] ?? [];

			foreach ($entities as $entity) {
				$this->em->detach($entity);
			}
		}
		$this->em->persist($blInvoice);
		$this->em->flush();

		return true;
	}

	private function openConnection(): void
	{
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration(), $this->em->getEventManager());
		}
	}

	public function getById(string $entity, string $id): ?object
	{
		return match ($entity) {
			BoostlingoQueueService::ENTITY_NAME_CONTACT => $this->em->getRepository(BlContact::class)->findOneBy(['id' => intval($id)]),
			BoostlingoQueueService::ENTITY_NAME_CUSTOMER => $this->em->getRepository(BlCustomer::class)->findOneBy(['id' => intval($id)]),
			BoostlingoQueueService::ENTITY_NAME_CALL => $this->em->getRepository(BlCall::class)->findOneBy(['id' => intval($id)]),
		};
	}
}
