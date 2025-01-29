<?php

namespace App\Command\Services;

use App\Model\Entity\HsCustomer;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use HubSpot\Crm\ObjectType;
use SevenShores\Hubspot\Http\Response;
use App\Connector\Hubspot\HubspotConnector;
use App\Linker\Services\HubspotQueueService;
use Symfony\Component\Console\Output\OutputInterface;
use HubSpot\Client\Crm\Objects\Model\SimplePublicObject;
use HubSpot\Client\Crm\Objects\Model\CollectionResponseSimplePublicObjectWithAssociationsForwardPaging;

class HubspotFetchService
{
	public const ENTITIES = [
		HubspotQueueService::ENTITY_NAME_CUSTOMER,
		HubspotQueueService::ENTITY_NAME_CONTACTS,
		HubspotQueueService::ENTITY_NAME_DEALS,
		HubspotQueueService::ENTITY_NAME_OWNERS,
		HubspotQueueService::ENTITY_NAME_MARKETING,
		HubspotQueueService::ENTITY_NAME_PIPELINES,
		HubspotQueueService::ENTITY_NAME_ENGAGEMENTS,
	];

	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private RedisClients $redisClients;
	private HubspotConnector $hsConnector;
	private HubspotQueueService $hsProcessQueueSrv;

	public function __construct(
		HubspotConnector $hsConnector,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		HubspotQueueService $hsProcessQueueSrv,
		RedisClients $redisClients,
	) {
		$this->em = $em;
		$this->hsConnector = $hsConnector;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->hsProcessQueueSrv = $hsProcessQueueSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function fetchAndEnqueue(string $entityName, OutputInterface $output, $id = null): mixed
	{
		$output->writeln(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;

		try {
			if (empty($id)) {
				if (HubspotQueueService::ENTITY_NAME_MARKETING !== $entityName) {
					$this->processExistingEntities($entityName, $output, $totalProcessed);
				} else {
					$this->processMarketingEntities($entityName, $output, $totalProcessed);
				}
			} else {
				if (HubspotQueueService::ENTITY_NAME_MARKETING === $entityName) {
					$output->writeln('This entity name does not have find by ID.');

					return false;
				}

				$object = $this->hsConnector->findById($entityName, $id);
				if (null !== $object) {
					return json_decode($object->toHeaderValue(), true);
				}

				return null;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing the Hubspot entity $entityName.", $thr);
			$output->writeln($thr->getMessage());
		}
		$output->writeln(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));

		return null;
	}

	public function updateRemote(string $entityName, OutputInterface $output): ?bool
	{
		$output->writeln(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;
		$totalIgnored = 0;

		try {
			if (HubspotQueueService::ENTITY_NAME_CUSTOMER !== $entityName) {
				$output->writeln(sprintf('Update remote only supports entities of type "%s"', HubspotQueueService::ENTITY_NAME_CUSTOMER));

				return false;
			}
			$entityList = [];
			$operation = null;
			switch ($entityName) {
				case HubspotQueueService::ENTITY_NAME_CUSTOMER:
					$operation = HubspotQueueService::OPERATION_UPDATE_REMOTE;
					$entityList = $this->em->getRepository(HsCustomer::class)->findAll();
					break;
			}
			foreach ($entityList as $entity) {
				if (empty($entity->getHsCustomerId())) {
					$output->writeln(sprintf('Ignoring %s ID: %s due empty remote hubspot customer id.', $entityName, $entity->getId()));
					++$totalIgnored;
					continue;
				}
				if (!$entity->getCustomer()) {
					$output->writeln(sprintf('Ignoring %s ID: %s due empty local customer id.', $entityName, $entity->getId()));
					++$totalIgnored;
					continue;
				}
				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => $operation,
					'data' => [
						'id' => $entity->getCustomer()->getId(),
					],
				];
				$output->writeln(sprintf('Adding %s ID: %s to the queue.', $entityName, $entity->getId()));
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, serialize($data));
				++$totalProcessed;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing remote update the Hubspot entity $entityName.", $thr);
			$output->writeln($thr->getMessage());
		}
		$output->writeln(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));
		$output->writeln(sprintf('IGNORED %s ROWS FOR ENTITY %s', $totalIgnored, $entityName));

		return null;
	}

	private function processExistingEntities(string $entityName, OutputInterface $output, &$totalProcessed): void
	{
		$after = null;
		$paginationKey = sprintf('%s-%s', RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, $entityName);
		if ($this->redisClients->redisMainDB->exists($paginationKey)) {
			$after = intval($this->redisClients->redisMainDB->get($paginationKey));
		}
		do {
			$associations = ObjectType::DEALS === $entityName ? 'companies' : null;
			$entitiesResponse = match ($entityName) {
				HubspotQueueService::ENTITY_NAME_PIPELINES => $this->hsConnector->findAllPipelines(),
				HubspotQueueService::ENTITY_NAME_ENGAGEMENTS => $this->hsConnector->findAllEngagement(HubspotQueueService::DEFAULT_PAGE_SIZE, $after),
				default => $this->hsConnector->findAll($entityName, HubspotQueueService::DEFAULT_PAGE_SIZE, $after, $associations),
			};
			$output->writeln("Fetching data after id $after");

			if (!$entitiesResponse) {
				$msg = "No entities were found in Hubspot for entity $entityName";
				$this->loggerSrv->addWarning($msg);
				$output->writeln($msg);
				break;
			}

			$this->preparePager($entitiesResponse, $paginationKey, $after);

			if ($entitiesResponse instanceof Response) {
				$objects = $entitiesResponse->data?->results;
			} else {
				$objects = $entitiesResponse->getResults();
			}

			foreach ($objects as $object) {
				if (null === $object) {
					continue;
				}
				$objectInfo = null;
				$objectId = null;
				if ($object instanceof \stdClass) {
					$objectInfo = json_decode(json_encode($object), true);
					$engagement = $objectInfo['engagement'] ?? null;
					if ($engagement && isset($engagement['type']) && ('EMAIL' === $engagement['type'] || 'INCOMING_EMAIL' === $engagement['type'])) {
						unset($objectInfo['metadata']['text'], $objectInfo['metadata']['html']);
					}
					$objectId = $object->engagement?->id;
				} else {
					$objectInfo = json_decode($object->toHeaderValue(), true);
					$objectId = $object->getId();
				}
				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => HubspotQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $objectInfo,
				];
				$output->writeln(sprintf('Adding %s ID: %s to the queue.', $entityName, $objectId));
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, serialize($data));
				++$totalProcessed;
			}
			if (!empty($after)) {
				$this->redisClients->redisMainDB->set($paginationKey, $after);
			}
		} while (!empty($after));
	}

	private function processMarketingEntities(string $entityName, OutputInterface $output, &$totalProcessed): void
	{
		$offset = 0;
		$totalRows = 0;
		$paginationKey = sprintf('%s-%s', RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, $entityName);
		if ($this->redisClients->redisMainDB->exists($paginationKey)) {
			$offset = intval($this->redisClients->redisMainDB->get($paginationKey));
		}
		do {
			$output->writeln("Fetching data offset id $offset");
			$entitiesResponse = $this->hsConnector->getMarketingEmails(HubspotQueueService::DEFAULT_PAGE_SIZE, $offset);
			if (!$entitiesResponse?->isSuccessfull()) {
				$msg = "No entities were found in Hubspot for entity $entityName";
				$this->loggerSrv->addWarning($msg);
				$output->writeln($msg);
				break;
			}

			if (count($entitiesResponse->getObjects())) {
				$totalRows = $entitiesResponse->getTotalCount();
				$offset = $entitiesResponse->getOffset() + HubspotQueueService::DEFAULT_PAGE_SIZE;
				$after = $offset;
			} else {
				$after = null;
				$this->redisClients->redisMainDB->del($paginationKey);
			}

			/** @var SimplePublicObject $object */
			foreach ($entitiesResponse->getObjects() as $object) {
				if (null === $object) {
					continue;
				}
				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => HubspotQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $object,
				];
				$output->writeln(sprintf('Adding %s ID: %s to the queue.', $entityName, $object['id']));
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, serialize($data));
				++$totalProcessed;
			}
			if ($offset < $totalRows) {
				$this->redisClients->redisMainDB->set($paginationKey, $after);
			}
		} while (!empty($after));
	}

	public function dequeueAndProcess(mixed $object, OutputInterface $output, $dequeueLimit = 10000): ?int
	{
		do {
			$totalProcessed = 0;
			if (null !== $object && !empty($object->data)) {
				$output->writeln('PROCESSING ONLY ONE ROW. NOT CALL TO QUEUE NEEDED.');

				if ($this->hsProcessQueueSrv->processEntity($object)) {
					++$totalProcessed;
				}
				break;
			}

			$output->writeln('PROCESSING HUBSPOT FETCH QUEUE.');
			$count = 1;
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE)) !== null) {
				if (null === $payload) {
					$msg = 'Hubspot queue is empty.';
					$this->loggerSrv->addWarning($msg);
					$output->writeln($msg);
					$dequeueLimit = 0;
				}
				try {
					if (($hubspotObj = unserialize($payload)) === false || !is_object($hubspotObj)) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}

					$processedResponse = $this->hsProcessQueueSrv->processEntity($hubspotObj);
					if (true !== $processedResponse) {
						$output->writeln("Entity $count Failed.");
						$this->enqueueDueError($hubspotObj, $output);
					} else {
						$output->writeln("Entity $count Processed.");
						++$totalProcessed;
					}
					++$count;
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Hubspot entity. Check logs for more details.', $thr);
					$this->enqueueDueError($payload, $output);
					continue;
				}
			}
		} while (0);
		$output->writeln(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));

		return null;
	}

	private function enqueueDueError($object, OutputInterface $output): void
	{
		if ($object->countFailed > RedisClients::DEFAULT_QUEUE_COUNT_FAILURE) {
			$id = $object->data['id'] ?? null;
			if (is_object($object?->data)) {
				$id = $object?->data->id;
			}
			$msg = "Hubspot Queue for entity name $object->entityName and ID $id exceeded the maximum of allowed  attempts. It will not be added to the queue";
			$this->loggerSrv->addError($msg, [$object]);
			$output->writeln($msg);
		} else {
			++$object->countFailed;
			$msg = "Adding again to queue the Hubspot entity name=>$object->entityName, ID=>{$object->data['id']}, failed=>$object->countFailed";
			$this->loggerSrv->addInfo($msg);
			$output->writeln($msg);
			$position = $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, serialize($object));
			if ($position < 0) {
				$this->loggerSrv->addError(
					'Unable to Enqueue the payload for Hubspot',
					[
						'Entity name' => $object->entityName,
						'ID' => $object->data['id'],
						'data' => $object,
					]
				);
				$output->writeln("Unable to enqueue again the Hubspot entity $object->entityName and ID {$object->data['id']}");
			}
		}
	}

	private function preparePager($response, $paginationKey, &$after): void
	{
		if ($response instanceof Response && isset($response->data) && $response->data?->hasMore) {
			$after = $response->data?->offset;

			return;
		}
		if ($response instanceof CollectionResponseSimplePublicObjectWithAssociationsForwardPaging && null !== $response->getPaging() && null !== $response->getPaging()->getNext() && null !== $response->getPaging()->getNext()->getAfter()) {
			$after = $response->getPaging()->getNext()->getAfter();

			return;
		}
		$after = null;
		$this->redisClients->redisMainDB->del($paginationKey);
	}
}
