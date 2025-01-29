<?php

namespace App\Linker\Handler;

use App\Message\ConnectorsHubspotProcessMessage;
use App\Service\LoggerService;
use App\Connector\Hubspot\HubspotConnector;
use App\Linker\Services\HubspotQueueService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HubspotHandler
{
	private LoggerService $loggerSrv;
	private HubspotConnector $hsConnector;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		HubspotConnector $hsConnector,
		ParameterBagInterface $parameterBag,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->hsConnector = $hsConnector;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WEBHOOKS);
		$this->bus = $bus;
	}

	public function processHubspotWebhook(array $payload): Response
	{
		if (isset($payload[0])) {
			$payload = $payload[0];
		}
		$event = $payload['subscriptionType'];
		$objectId = $payload['objectId'];
		$eventId = $payload['eventId'];
		$entityName = null;
		$dataArray = explode('.', $event);
		$name = $dataArray[0] ?? null;
		$action = $dataArray[1] ?? null;

		if (!in_array($action, [HubspotQueueService::OPERATION_CREATE, HubspotQueueService::OPERATION_UPDATE, HubspotQueueService::OPERATION_DELETE])) {
			$this->loggerSrv->addError("Hubspot webhook called with wrong action: $action");

			return new Response(null, Response::HTTP_BAD_REQUEST);
		}

		switch ($name) {
			case 'company':
				$entityName = HubspotQueueService::ENTITY_NAME_CUSTOMER;
				break;
			case 'contact':
				$entityName = HubspotQueueService::ENTITY_NAME_CONTACTS;
				break;
			case 'deal':
				$entityName = HubspotQueueService::ENTITY_NAME_DEALS;
				break;
			default:
				$this->loggerSrv->addError("Hubspot return unknown entity name to callback: name=>$entityName, id=>$eventId");

				return new Response(null, Response::HTTP_BAD_REQUEST);
		}

		switch ($action) {
			case HubspotQueueService::OPERATION_CREATE:
			case HubspotQueueService::OPERATION_UPDATE:
				$action = HubspotQueueService::OPERATION_CREATE_OR_UPDATE;
				$entityObject = $this->hsConnector->findById($entityName, $objectId);
				if (!$entityObject) {
					return new Response(null, Response::HTTP_BAD_REQUEST);
				}
				$objectData = json_decode($entityObject->toHeaderValue(), true);
				break;
			case HubspotQueueService::OPERATION_DELETE:
				$objectData = [
					'id' => $objectId,
				];
				break;
			default:
				$this->loggerSrv->addError("Hubspot call with unknown event: event=>$action, id=>$eventId");

				return new Response(null, Response::HTTP_BAD_REQUEST);
		}

		if ($entityName && $action) {
			$data = (object) [
				'countFailed' => 0,
				'entityName' => $entityName,
				'operation' => $action,
				'data' => $objectData,
			];
			$this->loggerSrv->addInfo(sprintf("WEBHOOK OPERATION $action=>Adding %s ID: %s to the queue.", $entityName, $objectId));
			try {
				$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($data)));
			} catch (\Throwable $thr) {
				$this->loggerSrv->addWarning($thr->getMessage());
			}
		}

		return new Response();
	}
}
