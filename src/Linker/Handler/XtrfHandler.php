<?php

namespace App\Linker\Handler;

use App\Message\ConnectorsHubspotProcessMessage;
use App\Service\XtrfWebhookService;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use App\Linker\Services\HubspotQueueService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class XtrfHandler
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WEBHOOKS);
		$this->bus = $bus;
	}

	public function processXtrfWebhook(?string $event, array $payload): Response
	{
		if (XtrfWebhookService::EVENT_CUSTOMER_UPDATED == $event && !empty($payload['id'])) {
			$objectData = ['id' => $payload['id']];

			$data = (object) [
				'countFailed' => 0,
				'entityName' => HubspotQueueService::ENTITY_NAME_CUSTOMER,
				'operation' => HubspotQueueService::OPERATION_UPDATE_REMOTE,
				'data' => $objectData,
			];
			$this->loggerSrv->addInfo(sprintf("WEBHOOK XTRF OPERATION $data->operation => Adding %s to the queue. ID: %s", HubspotQueueService::ENTITY_NAME_CUSTOMER, $payload['id']));
			//				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_WEBHOOK_QUEUE, serialize($data));
			try {
				$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($data)));
			} catch (\Throwable $tr) {
				$this->loggerSrv->addWarning($tr->getMessage());
			}
		}

		if (in_array($event, [
			XtrfWebhookService::EVENT_TASKS_FILES_READY,
			XtrfWebhookService::EVENT_PROJECT_CREATED,
			XtrfWebhookService::EVENT_PROJECT_STATUS_CHANGED,
			XtrfWebhookService::EVENT_QUOTE_CREATED,
			XtrfWebhookService::EVENT_QUOTE_STATUS_CHANGED,
			XtrfWebhookService::EVENT_JOB_STATUS_CHANGED,
			XtrfWebhookService::EVENT_CUSTOMER_CREATED,
			XtrfWebhookService::EVENT_CUSTOMER_UPDATED])) {
			$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_RULES_COMMAND_QUEUE, (array) serialize([
				'event' => $event,
				'object' => $payload,
			]));
			$this->loggerSrv->addInfo("WEBHOOK XTRF OPERATION $event=>Adding to the queue.");
		}


		return new Response();
	}
}
