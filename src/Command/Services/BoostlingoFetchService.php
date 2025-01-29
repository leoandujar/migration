<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Model\Entity\BlCustomer;
use App\Linker\Services\RedisClients;
use App\Linker\Services\BoostlingoQueueService;
use App\Connector\Boostlingo\BoostlingoConnector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BoostlingoFetchService.
 */
class BoostlingoFetchService
{
	public const ENTITIES = [
		BoostlingoQueueService::ENTITY_NAME_DICTIONARY,
		BoostlingoQueueService::ENTITY_NAME_CUSTOMER,
		BoostlingoQueueService::ENTITY_NAME_CONTACT,
		BoostlingoQueueService::ENTITY_NAME_CALL,
		BoostlingoQueueService::ENTITY_NAME_INVOICES,
		BoostlingoQueueService::ENTITY_NAME_INVOICES_CALLS,
	];

	private EntityManagerInterface $em;
	private ?string $datesRangeCallKey;
	private ?string $datesRangeInvoiceKey;
	private RedisClients $redisClients;
	private LoggerService $loggerService;
	private BoostlingoConnector $boostlingoConnector;
	private BoostlingoQueueService $boostlingoQueueService;

	public function __construct(
		EntityManagerInterface $em,
		BoostlingoConnector $boostlingoConnector,
		LoggerService $loggerService,
		RedisClients $redisClients,
		BoostlingoQueueService $boostlingoQueueService,
	) {
		$this->loggerService = $loggerService;
		$this->redisClients = $redisClients;
		$this->boostlingoConnector = $boostlingoConnector;
		$this->boostlingoQueueService = $boostlingoQueueService;
        $this->boostlingoQueueService->setBoostlingoFetchService($this);
		$this->datesRangeCallKey = sprintf('%s-%s', RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, 'call-datesRange');
		$this->datesRangeInvoiceKey = sprintf('%s-%s', RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, 'invoices-datesRange');
		$this->loggerService->setSubcontext(__CLASS__);
		$this->loggerService->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
		$this->em = $em;
	}

	public function fetchAndEnqueue(string $entityName, OutputInterface $output, ?string $startDate, ?string $endDate, $id = null): ?object
	{
		$output->writeln(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;

		try {
			if (empty($id)) {
				switch ($entityName) {
					case BoostlingoQueueService::ENTITY_NAME_DICTIONARY:
						$this->processExistingDictionaries($entityName, $output, $totalProcessed);
						break;
					case BoostlingoQueueService::ENTITY_NAME_CONTACT:
						$this->fetchContacts($entityName, $output, $totalProcessed);
						break;
					default:
						$this->processExistingEntities($entityName, $output, $totalProcessed, $startDate, $endDate);
				}
			} else {
				if (BoostlingoQueueService::ENTITY_NAME_DICTIONARY != $entityName) {
					$object = $this->boostlingoQueueService->getById($entityName, $id);
					if (null !== $object) {
						return $object;
					}
				}

				return null;
			}
		} catch (\Throwable $thr) {
			$this->loggerService->addError("Error processing the Bootslingo entity $entityName.", $thr);
			$output->writeln($thr->getMessage());
		}
		$output->writeln(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));

		return null;
	}

	private function fetchContacts(string $entityName, OutputInterface $output, &$totalProcessed): void
	{
		$entities = $this->em->getRepository(BlCustomer::class)->findAll();
		/** @var BlCustomer $entity */
		foreach ($entities as $entity) {
			$this->processExistingEntities($entityName, $output, $totalProcessed, null, null, $entity->getBlCustomerId());
		}
	}

	private function isLogged(OutputInterface $output): bool
	{
		try {
			$tokenExpiresAt = $this->boostlingoConnector->getTokenExpiresAt();
			if (null !== $tokenExpiresAt) {
				$dateExpiresAt = new \DateTime($tokenExpiresAt);
				$token = $this->boostlingoConnector->getToken();
				$now = new \DateTime('now');
				if (($dateExpiresAt > $now) && false !== $token) {
					return true;
				}
			}
		} catch (\Throwable $thr) {
			$this->loggerService->addError('Error checking boostlingo login', $thr);
			$output->writeln($thr->getMessage());
		}

		return false;
	}

	private function getDateRange(): array
	{
		$dateNow = new \DateTime('now');
		$dateTwoMonthForward = new \DateTime('-2 month');

		return [$dateTwoMonthForward->format('Y-m-d'), $dateNow->format('Y-m-d')];
	}

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

	/**
	 * @return null
	 */
	public function processExistingDictionaries(string $entityName, OutputInterface $output, &$totalProcessed): void
	{
		if (!$this->isLogged($output)) {
			if (null === $this->boostlingoConnector->signIn()) {
				$msg = 'User or password incorrect';
				$this->loggerService->addWarning($msg);
				$output->writeln($msg);

				return;
			}
		}

		$entitiesResponse = $this->boostlingoConnector->dictionaries();

		$output->writeln('Fetching data');

		if (!isset($entitiesResponse->getRaw()['languages'])) {
			$msg = "No entities were found in Boostlingo for entity $entityName";
			$this->loggerService->addWarning($msg);
			$output->writeln($msg);

			return;
		}

		foreach (BoostlingoQueueService::FIELDS_DICTIONARY as $field) {
			$objects = $entitiesResponse->getRaw()[$field];
			if (!count($objects)) {
				$msg = "No entities were found in Boostlingo for entity $entityName";
				$this->loggerService->addWarning($msg);
			}

			foreach ($objects as $object) {
				if (null === $object) {
					continue;
				}

				$objectId = (isset($object['id'])) ? $object['id'] : $object['data']['id'];

				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => BoostlingoQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $object,
					'field' => $field,
				];

				$output->writeln(sprintf('Adding %s ID: %s to the queue.', $entityName, $objectId));
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, serialize($data));
				++$totalProcessed;
			}
		}
	}

	private function processExistingEntities(string $entityName, OutputInterface $output, &$totalProcessed, ?string $startDate, ?string $endDate, $customerId = null): void
	{
		$after = null;
		$paginationKey = sprintf('%s-%s', RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, $entityName);

		if ($this->redisClients->redisMainDB->exists($paginationKey)) {
			$after = intval($this->redisClients->redisMainDB->get($paginationKey));
		}

		do {
			if (!$this->isLogged($output)) {
				if (null === $this->boostlingoConnector->signIn()) {
					$msg = 'User or password incorrect';
					$this->loggerService->addWarning($msg);
					$output->writeln($msg);
					break;
				}
			}
			$urlQuery = $this->buildQueryString($entityName, $after, $startDate, $endDate, $customerId);
			$entitiesResponse = match ($entityName) {
				BoostlingoQueueService::ENTITY_NAME_CONTACT => $this->boostlingoConnector->clientsUserList($urlQuery),
				BoostlingoQueueService::ENTITY_NAME_CUSTOMER => $this->boostlingoConnector->clientsClient($urlQuery),
				BoostlingoQueueService::ENTITY_NAME_INVOICES => $this->boostlingoConnector->getInvoices($urlQuery),
				BoostlingoQueueService::ENTITY_NAME_CALL => $this->boostlingoConnector->callLog($urlQuery)
			};

			$output->writeln("Fetching data after id $after");

			if (!isset($entitiesResponse->getRaw()['items'])) {
				$msg = "No entities were found in Boostlingo for entity $entityName";
				$this->loggerService->addWarning($msg);
				$output->writeln($msg);
				break;
			}

			$this->preparePager($entitiesResponse, $paginationKey, $after, $entityName, $startDate, $endDate);

			$objects = $entitiesResponse->getRaw()['items'];

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

				$output->writeln(sprintf('Adding %s ID: %s to the queue.', $entityName, $objectId));
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, serialize($data));
				++$totalProcessed;
			}
			if (!empty($after)) {
				$this->redisClients->redisMainDB->set($paginationKey, $after);
			}
		} while (!empty($after));
	}

	public function dequeueAndProcess($object, OutputInterface $output, $dequeueLimit = 10000): ?int
	{
		do {
			$totalProcessed = 0;
			if (null !== $object) {
				$output->writeln('PROCESSING ONLY ONE ROW. NOT CALL TO QUEUE NEEDED.');

				if ($this->boostlingoQueueService->processEntity($object, $output)) {
					++$totalProcessed;
				}
				break;
			}

			$output->writeln('PROCESSING BOOSTLINGO FETCH QUEUE.');
			$count = 1;
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE)) !== null) {
				if (null === $payload) {
					$msg = 'Boostlingo queue is empty.';
					$this->loggerService->addWarning($msg);
					$output->writeln($msg);
					$dequeueLimit = 0;
				}
				try {
					if (($boostLingoObj = unserialize($payload)) === false || !is_object($boostLingoObj)) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}

					$processedResponse = $this->boostlingoQueueService->processEntity($boostLingoObj, $output);
					if (true !== $processedResponse) {
						$output->writeln("Entity $count Failed.");
						$this->enqueueDueError($boostLingoObj, $output);
					} else {
						$output->writeln("Entity $count Processed.");
						++$totalProcessed;
					}
					++$count;
				} catch (\Throwable $thr) {
					$this->loggerService->addError('Error processing Boostlingo entity. Check logs for more details.', $thr);
					$this->enqueueDueError($payload, $output);
					continue;
				}
			}
		} while (0);
		$output->writeln(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));

		return null;
	}

	private function enqueueDueError(mixed $object, OutputInterface $output): void
	{
		$id = $object?->data->id ?? $object->data['id'] ?? $object->data['data']['id'] ?? null;
		if ($object->countFailed > RedisClients::DEFAULT_QUEUE_COUNT_FAILURE) {
			$msg = "Boostlingo Queue for entity name $object->entityName and ID $id exceeded the maximum of allowed  attempts. It will not be added to the queue";
			$this->loggerService->addError($msg, [$object]);
			$output->writeln($msg);
		} else {
			++$object->countFailed;
			$msg = "Adding again to queue the Boostlingo entity name=>$object->entityName, ID=>$id, failed=>$object->countFailed";
			$this->loggerService->addInfo($msg);
			$output->writeln($msg);
			$position = $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE, serialize($object));
			if ($position < 0) {
				$this->loggerService->addError(
					'Unable to Enqueue the payload for Boostlingo',
					[
						'Entity name' => $object->entityName,
						'ID' => $id,
						'data' => $object,
					]
				);
				$output->writeln("Unable to enqueue again the Boostlingo entity $object->entityName and ID $id ");
			}
		}
	}

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
