<?php

namespace App\MessageHandler;

use App\Connector\Hubspot\HubspotConnector;
use App\Linker\Services\HubspotQueueService;
use App\Linker\Services\RedisClients;
use App\Message\ConnectorsHubspotFetchEngagementMessage;
use SevenShores\Hubspot\Http\Response;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConnectorsHubspotFetchEngagementMessageHandler
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private HubspotConnector $hsConnector;

	public function __construct(
		RedisClients $redisClients,
		HubspotConnector $hsConnector,
		LoggerService $loggerSrv,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->hsConnector = $hsConnector;
	}

	public function __invoke(ConnectorsHubspotFetchEngagementMessage $message): void
	{

		$entityName = HubspotQueueService::ENTITY_NAME_ENGAGEMENTS;
		$this->loggerSrv->addInfo('Collecting entities to process.');
		$this->loggerSrv->addInfo('COLLECTING ENTITIES OF TYPE "%s"', $entityName);
		$totalProcessed = 0;

		try {
			$this->processEntity($totalProcessed);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing the Hubspot entity $entityName.", $thr);
		}
		$this->loggerSrv->addInfo("PROCESSED %s ROWS FOR ENTITY %s .$totalProcessed, $entityName");
	}

	private function processEntity(&$totalProcessed): void
	{
		$after = null;
		$total = 0;
		$entityName = HubspotQueueService::ENTITY_NAME_ENGAGEMENTS;
		$paginationKey = sprintf('%s-%s', RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE, $entityName);
		if ($this->redisClients->redisMainDB->exists($paginationKey)) {
			$after = intval($this->redisClients->redisMainDB->get($paginationKey));
		}
		do {
			$entitiesResponse = $this->hsConnector->findAllModifiedEngagement(HubspotQueueService::DEFAULT_PAGE_SIZE, $after);

			if (!$entitiesResponse) {
				$msg = 'No entities were found in Hubspot for entity '.$entityName;
				$this->loggerSrv->addWarning($msg);
				break;
			}

			$this->preparePager($entitiesResponse, $paginationKey, $after);

			$objects = $entitiesResponse->data?->results ?? [];
			$total = $entitiesResponse?->data?->total ?? 0;

			foreach ($objects as $object) {
				if (null === $object) {
					continue;
				}
				$objectInfo = null;
				$objectId = null;
				if ($object instanceof \stdClass) {
					$objectInfo = json_decode(json_encode($object), true);
					$objectId = $object?->engagement?->id;
				}
				$data = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => HubspotQueueService::OPERATION_UPDATE,
					'data' => $objectInfo,
				];
				$this->loggerSrv->addInfo(sprintf('Adding %d OF %d %s ID: %s to the queue.', $totalProcessed, $total, $entityName, $objectId));
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_WEBHOOK_QUEUE, serialize($data));
				++$totalProcessed;
			}
			if (!empty($after)) {
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
		$after = null;
		$this->redisClients->redisMainDB->del($paginationKey);
	}
}
