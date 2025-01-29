<?php

namespace App\MessageHandler;

use App\Command\Services\BoostlingoFetchService;
use App\Connector\Boostlingo\BoostlingoConnector;
use App\Linker\Services\BoostlingoQueueService;
use App\Linker\Services\RedisClients;
use App\Message\ConnectorsBoostlingoFetchMessage;
use App\Message\ConnectorsBoostlingoProcessMessage;
use App\Model\Entity\BlCall;
use App\Model\Entity\BlCustomer;
use App\Model\Entity\BlProviderInvoice;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ConnectorsBoostlingoFetchMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private BoostlingoConnector $boostlingoConn;
	private BoostlingoQueueService $boostlingoQueueService;
	private BoostlingoConnector $boostlingoConnector;
	private RedisClients $redisClients;
	private MessageBusInterface $bus;
	private ?string $datesRangeCallKey;

	private ?string $datesRangeInvoiceKey;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		BoostlingoConnector $boostlingoConn,
		BoostlingoQueueService $boostlingoQueueService,
		BoostlingoConnector $boostlingoConnector,
		RedisClients $redisClients,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
		$this->boostlingoConn = $boostlingoConn;
		$this->boostlingoQueueService = $boostlingoQueueService;
		$this->boostlingoConnector = $boostlingoConnector;
		$this->redisClients = $redisClients;
		$this->datesRangeCallKey = sprintf('%s-%s', RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, 'call-datesRange');
		$this->datesRangeInvoiceKey = sprintf('%s-%s', RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, 'invoices-datesRange');
		$this->bus = $bus;
	}

	/**
	 * @throws \DateMalformedStringException
	 * @throws \Exception
	 */
	public function __invoke(ConnectorsBoostlingoFetchMessage $message): void
	{
		$entityId = $message->getId();
		$targetEntity = $message->getEntity();
		$startDate = $message->getStartDate();
		$endDate = $message->getEndDate();
		$since = $message->getSince();
		$onlyDequeue = $message->getOnlyDequeue();

		if (!empty($entityId) && empty($targetEntity)) {
			$msg = 'You provided entity ID but not entity name.';
			$this->loggerSrv->addError("BoostlingoService=> $msg");
			throw new \Exception($msg);
		}
		if ($since) {
			$endDate = (new \DateTime())->format('Y-m-d\TH:i:s.u\Z');
			$startDate = (new \DateTime())->modify("- $since")->format('Y-m-d\TH:i:s.u\Z');
		}
		if (($startDate xor $endDate) && !$since) {
			$msg = 'You need to provide both start_date and end_date';
			$this->loggerSrv->addError("BoostlingoService=> $msg");
			throw new \Exception($msg);
		}

		if ($startDate && $endDate) {
			$startDate = (new \DateTime($startDate))->format('Y-m-d\TH:i:s.u\Z');
			$endDate = (new \DateTime($endDate))->format('Y-m-d\TH:i:s.u\Z');
		}

		$entitiesNames = BoostlingoFetchService::ENTITIES;
		if (!empty($targetEntity)) {
			if (in_array(strtolower($targetEntity), $entitiesNames)) {
				$entitiesNames = [strtolower($targetEntity)];
			} else {
				throw new \Exception("The entity name $targetEntity does not exists in our list.");
			}
		}

		if ('invoices_calls' === $targetEntity) {
			$this->processEntities();

			return;
		}

		$this->loggerSrv->addInfo('Collecting entities to process.');
		foreach ($entitiesNames as $entityName) {
			$this->loggerSrv->addInfo("Starting processing for entity $entityName");
			$responseObject = null;
			if (1 !== $onlyDequeue) {
				$responseObject = $this->fetchAndEnqueue(
					entityName: $entityName,
					startDate: $startDate,
					endDate: $endDate,
					id:  $entityId
				);
			}
			if (null !== $responseObject && false !== $responseObject) {
				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => BoostlingoQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $responseObject,
				];
				try {
					$this->bus->dispatch(new ConnectorsBoostlingoProcessMessage(serialize($data)));
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError("BoostlingoService error send message=> $thr");
				}
			}
		}
	}

	public function processEntities(): void
	{
		try {
			$blInvoiceList = $this->em->getRepository(BlProviderInvoice::class)->findAll();
			$totalProcessedInvoices = $totalProcessedCalls = $totalSuccessInvoices = $totalSuccessCalls = $totalFailedInvoices = $totalFailedICalls = 0;

			if (!$this->isLogged()) {
				if (null === $this->boostlingoConn->signIn()) {
					$msg = 'User or password incorrect or unable to login into Boostlingo. ';
					$this->loggerSrv->addWarning($msg);

					return;
				}
			}
			/** @var BlProviderInvoice $blInvoice */
			foreach ($blInvoiceList as $blInvoice) {
				if (!$blInvoice->getBlProviderInvoiceId()) {
					++$totalFailedInvoices;
					continue;
				}
				++$totalProcessedInvoices;
				$this->loggerSrv->addInfo("Fetching data for Provider Invoice {$blInvoice->getBlProviderInvoiceId()}");
				$response = $this->boostlingoConn->retrieveInvoice($blInvoice->getBlProviderInvoiceId());
				if (!$response->isSuccessfull()) {
					$this->loggerSrv->addError("Error in retrieve invoice #{$blInvoice->getBlProviderInvoiceId()}=>{$response->getErrorMessage()}");
					++$totalFailedInvoices;
					continue;
				}

				$data = $response->getRaw();
				if (!$data) {
					$this->loggerSrv->addWarning("Remote Boostlingo Invoice #{$blInvoice->getBlProviderInvoiceId()} return no data.");
					++$totalFailedInvoices;
					continue;
				}

				++$totalSuccessInvoices;
				$calls = $data['calls'] ?? [];

				foreach ($calls as $call) {
					/** @var BlCall $blCallObj */
					$blCallObj = $this->em->getRepository(BlCall::class)->findOneBy(['blCallId' => $call['callLogId']]);
					if (!$blCallObj) {
						$this->loggerSrv->addNotice("BlCall for ID {$call['callLogId']} is not on DB. Skipping.");
						++$totalFailedICalls;
						continue;
					}
					$blCallObj->setBlProviderInvoiceId($blInvoice->getBlProviderInvoiceId());
					$this->em->persist($blCallObj);
					++$totalSuccessCalls;
					++$totalProcessedCalls;
				}
			}
			$this->em->flush();
			$this->loggerSrv->addInfo("TOTAL PROCESSED INVOICES $totalProcessedInvoices");
			$this->loggerSrv->addInfo("TOTAL SUCCESS INVOICES $totalSuccessInvoices");
			$this->loggerSrv->addInfo("TOTAL FAILED INVOICES $totalFailedInvoices");
			$this->loggerSrv->addInfo("TOTAL PROCESSED CALLS $totalProcessedCalls");
			$this->loggerSrv->addInfo("TOTAL SUCCESS CALLS$totalSuccessCalls");
			$this->loggerSrv->addInfo("TOTAL FAILED CALLS $totalFailedICalls");
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving the Bootslingo invoices.', $thr);
		}
	}

	private function isLogged(): bool
	{
		try {
			$tokenExpiresAt = $this->boostlingoConn->getTokenExpiresAt();
			if (null !== $tokenExpiresAt) {
				$dateExpiresAt = new \DateTime($tokenExpiresAt);
				$token = $this->boostlingoConn->getToken();
				$now = new \DateTime('now');
				if (($dateExpiresAt > $now) && false !== $token) {
					return true;
				}
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error checking boostlingo login', $thr);
		}

		return false;
	}

	public function fetchAndEnqueue(string $entityName, ?string $startDate, ?string $endDate, $id = null): mixed
	{
		$this->loggerSrv->addInfo(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;

		try {
			if (empty($id)) {
				switch ($entityName) {
					case BoostlingoQueueService::ENTITY_NAME_DICTIONARY:
						$this->processExistingDictionaries($entityName, $totalProcessed);
						break;
					case BoostlingoQueueService::ENTITY_NAME_CONTACT:
						$this->fetchContacts($entityName, $totalProcessed);
						break;
					default:
						$this->processExistingEntities($entityName, $totalProcessed, $startDate, $endDate);
				}
			} else {
				if (BoostlingoQueueService::ENTITY_NAME_DICTIONARY != $entityName) {
					$object = $this->boostlingoQueueService->getById($entityName, $id);
					if (null !== $object) {
						return $object;
					}
				}

				return false;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing the Bootslingo entity $entityName.", $thr);
			$this->loggerSrv->addInfo($thr->getMessage());
		}
		$this->loggerSrv->addInfo(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));

		return null;
	}

	/**
	 * @throws \RedisException
	 */
	public function processExistingDictionaries(string $entityName, &$totalProcessed): void
	{
		if (!$this->isLogged()) {
			if (null === $this->boostlingoConnector->signIn()) {
				$msg = 'User or password incorrect';
				$this->loggerSrv->addWarning($msg);

				return;
			}
		}

		$entitiesResponse = $this->boostlingoConnector->dictionaries();

		if (!isset($entitiesResponse->getRaw()['languages'])) {
			$msg = "No entities were found in Boostlingo for entity $entityName";
			$this->loggerSrv->addWarning($msg);

			return;
		}
		$this->loggerSrv->addInfo('PROCESSING BOOSTLINGO FETCH QUEUE.');
		foreach (BoostlingoQueueService::FIELDS_DICTIONARY as $field) {
			$objects = $entitiesResponse->getRaw()[$field];
			if (!count($objects)) {
				$msg = "No entities were found in Boostlingo for entity $entityName";
				$this->loggerSrv->addWarning($msg);
			}

			$count = 1;
			foreach ($objects as $object) {
				if (null === $object) {
					continue;
				}


				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => BoostlingoQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $object,
					'field' => $field,
				];
				$this->loggerSrv->addInfo(sprintf('Entity %s ID: %s Number Processed.', $entityName, $count++));
				try {
					$this->bus->dispatch(new ConnectorsBoostlingoProcessMessage(serialize($data)));
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError("BoostlingoService error send message=> $thr");
				}
				++$totalProcessed;
			}
		}
	}

	private function fetchContacts(string $entityName, &$totalProcessed): void
	{
		$entities = $this->em->getRepository(BlCustomer::class)->findAll();
		/** @var BlCustomer $entity */
		foreach ($entities as $entity) {
			$this->processExistingEntities($entityName, $totalProcessed, null, null, $entity->getBlCustomerId());
		}
	}

	/**
	 * @throws \RedisException
	 * @throws \DateMalformedStringException
	 */
	private function processExistingEntities(string $entityName, &$totalProcessed, ?string $startDate, ?string $endDate, $customerId = null): void
	{
		$after = null;
		$paginationKey = sprintf('%s-%s', RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, $entityName);

		if ($this->redisClients->redisMainDB->exists($paginationKey)) {
			$after = intval($this->redisClients->redisMainDB->get($paginationKey));
		}

		$this->loggerSrv->addInfo('PROCESSING BOOSTLINGO FETCH QUEUE.');
		do {
			if (!$this->isLogged()) {
				if (null === $this->boostlingoConnector->signIn()) {
					$msg = 'User or password incorrect';
					$this->loggerSrv->addWarning($msg);
					break;
				}
			}
			$urlQuery = $this->buildQueryString($entityName, $after, $startDate, $endDate, $customerId);
			$entitiesResponse = match ($entityName) {
				BoostlingoQueueService::ENTITY_NAME_CONTACT => $this->boostlingoConnector->clientsUserList($urlQuery),
				BoostlingoQueueService::ENTITY_NAME_CUSTOMER => $this->boostlingoConnector->clientsClient($urlQuery),
				BoostlingoQueueService::ENTITY_NAME_INVOICES => $this->boostlingoConnector->getInvoices($urlQuery),
				BoostlingoQueueService::ENTITY_NAME_CALL => $this->boostlingoConnector->callLog($urlQuery),
			};

			$this->loggerSrv->addInfo("Fetching data after id $after");

			if (!isset($entitiesResponse->getRaw()['items'])) {
				$msg = "No entities were found in Boostlingo for entity $entityName";
				$this->loggerSrv->addWarning($msg);
				break;
			}

			$this->preparePager($entitiesResponse, $paginationKey, $after, $entityName, $startDate, $endDate);

			$objects = $entitiesResponse->getRaw()['items'];

			$count = 1;
			foreach ($objects as $object) {
				if (null === $object) {
					continue;
				}
				$objectId = $object['id'] ?? $object['data']['id'];

				if (null !== $customerId) {
					$object['idCustomer'] = $customerId;
				}

				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => BoostlingoQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $object,
				];

				$this->loggerSrv->addInfo(sprintf('Entity %s ID: %s Number Processed:%s', $entityName, $objectId, ++$count));
				try {
					$this->bus->dispatch(new ConnectorsBoostlingoProcessMessage(serialize($data)));
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError("BoostlingoService error send message=> $thr");
				}
				++$totalProcessed;
			}
			if (!empty($after)) {
				$this->redisClients->redisMainDB->set($paginationKey, $after);
			}
		} while (!empty($after));
	}

	/**
	 * @throws \RedisException
	 */
	private function buildQueryString(string $entityName, ?int &$offset, ?string $startDate, ?string $endDate, $customerId = null): bool|string
	{
		if (null === $offset) {
			$offset = 1;
		}
		$response = [];
		$response['pageSize'] = BoostlingoQueueService::DEFAULT_PAGE_SIZE;
		$response['pageIndex'] = $offset;
		switch ($entityName) {
			case BoostlingoQueueService::ENTITY_NAME_INVOICES:
				$response['sortBy'] = 'createdDate';
				$response['sortDirection'] = 2;
				break;
			case BoostlingoQueueService::ENTITY_NAME_CONTACT:
				$response['companyAccountId'] = $customerId;
				break;
			case BoostlingoQueueService::ENTITY_NAME_CALL:
				$dateRange = $this->getDateRange();
				if ($this->redisClients->redisMainDB->exists($this->datesRangeCallKey)) {
					$aux = $this->redisClients->redisMainDB->get($this->datesRangeCallKey);
					$dateRange = explode('*', $aux);
				} else {
					$stringDateRange = implode('*', $dateRange);
					$this->redisClients->redisMainDB->set($this->datesRangeCallKey, $stringDateRange);
				}
				$response['dateSince'] = $dateRange[0];
				$response['dateTill'] = $dateRange[1];
				$response['companyAccountId'] = $this->boostlingoConnector->getCompanyAccountId();
				$response['userAccountId'] = 0;
				$response['requestorId'] = 0;

				if ($startDate && $endDate) {
					$response['dateSince'] = $startDate;
					$response['dateTill'] = $endDate;
				}
				break;
		}

		return json_encode($response);
	}

	private function getDateRange(): array
	{
		$dateNow = new \DateTime('now');
		$dateTwoMonthForward = new \DateTime('-2 month');

		return [$dateTwoMonthForward->format('Y-m-d'), $dateNow->format('Y-m-d')];
	}

	/**
	 * @throws \DateMalformedStringException
	 * @throws \RedisException
	 */
	private function preparePager(mixed $response, $paginationKey, &$after, $entityName, ?string $startDate, ?string $endDate): void
	{
		$itemsCount = count($response->getRaw()['items']);
		$index = $response->getRaw()['pageIndex'];
		$totalPages = $response->getRaw()['totalPages'];
		if (BoostlingoQueueService::ENTITY_NAME_CALL === $entityName) {
			$now = (new \DateTime('now'))->format('Y-m-d\TH:i:s.u\Z');
			$range = [
				$now,
				$now,
			];
			if ($this->redisClients->redisMainDB->exists($this->datesRangeCallKey)) {
				$savedDate = $this->redisClients->redisMainDB->get($this->datesRangeCallKey);
				$range = explode('*', $savedDate);
			}

			if (0 !== $totalPages && ($index < $totalPages)) {
				$after = $index + 1;

				return;
			}

			if (0 !== $totalPages && $after >= 1 && !$startDate && !$endDate) {
				$after = 1;
				$dateSince = (new \DateTime($range[0]))->modify('-2 months')->format('Y-m-d\TH:i:s.u\Z');
				$rangeModified = $dateSince.'*'.$range[0];
				$this->redisClients->redisMainDB->set($this->datesRangeCallKey, $rangeModified);

				return;
			}
		} elseif ($itemsCount > 0 && $index < $totalPages) {
			$after = $index + 1;

			return;
		}
		$after = null;
		$this->redisClients->redisMainDB->del($paginationKey);
		$this->redisClients->redisMainDB->del($this->datesRangeCallKey);
	}
}
