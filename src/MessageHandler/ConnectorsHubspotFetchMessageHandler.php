<?php

namespace App\MessageHandler;

use App\Command\Services\HubspotFetchService;
use App\Connector\Hubspot\HubspotConnector;
use App\Linker\Services\HubspotQueueService;
use App\Linker\Services\RedisClients;
use App\Message\ConnectorsHubspotFetchMessage;
use App\Message\ConnectorsHubspotProcessMessage;
use App\Model\Entity\HsCustomer;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use HubSpot\Client\Crm\Objects\Model\CollectionResponseSimplePublicObjectWithAssociationsForwardPaging;
use HubSpot\Client\Crm\Objects\Model\SimplePublicObject;
use HubSpot\Crm\ObjectType;
use SevenShores\Hubspot\Http\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ConnectorsHubspotFetchMessageHandler
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
	private HubspotConnector $hsConnector;
	private RedisClients $redisClients;
	private MessageBusInterface $bus;

	public function __construct(
		HubspotConnector $hsConnector,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		RedisClients $redisClients,
		MessageBusInterface $bus,
	) {
		$this->em = $em;
		$this->hsConnector = $hsConnector;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->bus = $bus;
	}

	public function __invoke(ConnectorsHubspotFetchMessage $message): void
	{
		$entityId = $message->getId();
		$targetEntity = $message->getEntity();
		$onlyDequeue = $message->getOnlyDequeue();
		$updateRemote = $message->getUpdateRemote();
		if (!empty($entityId) && empty($targetEntity)) {
			$msg = 'You provided entity ID but not entity name.';
			$this->loggerSrv->addError("HubspotService=> $msg");

			return;
		}

		$entitiesNames = HubspotFetchService::ENTITIES;
		if (null !== $targetEntity) {
			if (in_array(strtolower($targetEntity), $entitiesNames)) {
				$entitiesNames = [strtolower($targetEntity)];
			} else {
				$this->loggerSrv->addError("The entity name $targetEntity does not exists in our list.");

				return;
			}
		}
		$this->loggerSrv->addInfo('Collecting entities to process.');
		foreach ($entitiesNames as $entityName) {
			$this->loggerSrv->addInfo("Starting processing for entity $entityName");
			/**
			 * In case that $responseObject has value, it means that command was called with params --id
			 * When this happens we don't need to enqueue the object and fetch the queue again.
			 * Only we need to directly process the $object fetched from Hubspot without enqueue and dequeue.
			 * In case that we enqueue, we lost the reference to the object and we need to iterate all objects into
			 * the redis queue for finding the specific one.
			 */
			$responseObject = null;

			if (1 === $updateRemote) {
				$this->updateRemote(entityName: $entityName);
			}
			if (1 !== $onlyDequeue && 1 !== $updateRemote) {
				$responseObject = $this->fetchAndEnqueue(entityName:$entityName, id:$entityId);
			}
			if (null !== $responseObject && false !== $responseObject) {
				$responseObject = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => HubspotQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $responseObject,
				];
				try {
					$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($responseObject)));
				} catch (\Throwable $tr) {
					$this->loggerSrv->addWarning($tr->getMessage());
				}
			}
		}

	}

	public function fetchAndEnqueue(string $entityName, $id = null): mixed
	{
		$this->loggerSrv->addInfo(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;

		try {
			if (empty($id)) {
				if (HubspotQueueService::ENTITY_NAME_MARKETING !== $entityName) {
					$this->processExistingEntities($entityName, $totalProcessed);
				} else {
					$this->processMarketingEntities($entityName, $totalProcessed);
				}
			} else {
				if (HubspotQueueService::ENTITY_NAME_MARKETING === $entityName) {
					$this->loggerSrv->addWarning('This entity name does not have find by ID.');

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
		}
		$this->loggerSrv->addInfo(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));

		return null;
	}

	public function updateRemote(string $entityName): ?bool
	{
		$this->loggerSrv->addInfo(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;
		$totalIgnored = 0;

		try {
			if (HubspotQueueService::ENTITY_NAME_CUSTOMER !== $entityName) {
				$this->loggerSrv->addWarning(sprintf('Update remote only supports entities of type "%s"', HubspotQueueService::ENTITY_NAME_CUSTOMER));

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
					$this->loggerSrv->addWarning(sprintf('Ignoring %s ID: %s due empty remote hubspot customer id.', $entityName, $entity->getId()));
					++$totalIgnored;
					continue;
				}
				if (!$entity->getCustomer()) {
					$this->loggerSrv->addWarning(sprintf('Ignoring %s ID: %s due empty local customer id.', $entityName, $entity->getId()));
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
				try {
					$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($data)));
				} catch (\Throwable $tr) {
					$this->loggerSrv->addWarning($tr->getMessage());
				}
				++$totalProcessed;
			}
			$this->loggerSrv->addInfo(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing remote update the Hubspot entity $entityName.", $thr);
		}
		$this->loggerSrv->addInfo(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));
		$this->loggerSrv->addWarning(sprintf('IGNORED %s ROWS FOR ENTITY %s', $totalIgnored, $entityName));

		return null;
	}

	private function processExistingEntities(string $entityName, &$totalProcessed): void
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

			if (!$entitiesResponse) {
				$msg = "No entities were found in Hubspot for entity $entityName";
				$this->loggerSrv->addWarning($msg);
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
				if ($object instanceof \stdClass) {
					$objectInfo = json_decode(json_encode($object), true);
					$engagement = $objectInfo['engagement'] ?? null;
					if ($engagement && isset($engagement['type']) && ('EMAIL' === $engagement['type'] || 'INCOMING_EMAIL' === $engagement['type'])) {
						unset($objectInfo['metadata']['text'], $objectInfo['metadata']['html']);
					}
				} else {
					$objectInfo = json_decode($object->toHeaderValue(), true);

				}
				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => HubspotQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $objectInfo,
				];
				try {
					$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($data)));
				} catch (\Throwable $tr) {
					$this->loggerSrv->addWarning($tr->getMessage());
				}

				++$totalProcessed;
			}
			if (!empty($after)) {
				$this->redisClients->redisMainDB->set($paginationKey, $after);
				$this->loggerSrv->addInfo(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));
			}

		} while (!empty($after));
	}

	private function processMarketingEntities(string $entityName, &$totalProcessed): void
	{
		$offset = 0;
		$totalRows = 0;
		$paginationKey = sprintf('%s-%s', RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, $entityName);
		if ($this->redisClients->redisMainDB->exists($paginationKey)) {
			$offset = intval($this->redisClients->redisMainDB->get($paginationKey));
		}
		do {
			$this->loggerSrv->addInfo("Fetching data offset id $offset");
			$entitiesResponse = $this->hsConnector->getMarketingEmails(HubspotQueueService::DEFAULT_PAGE_SIZE, $offset);
			if (!$entitiesResponse?->isSuccessfull()) {
				$msg = "No entities were found in Hubspot for entity $entityName";
				$this->loggerSrv->addWarning($msg);
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
				try {
					$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($data)));
				} catch (\Throwable $tr) {
					$this->loggerSrv->addWarning($tr->getMessage());
				}
				++$totalProcessed;
			}

			$this->loggerSrv->addInfo(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));
			if ($offset < $totalRows) {
				$this->redisClients->redisMainDB->set($paginationKey, $after);
			}
		} while (!empty($after));
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
