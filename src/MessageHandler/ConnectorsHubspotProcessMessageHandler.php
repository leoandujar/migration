<?php

namespace App\MessageHandler;

use App\Message\ConnectorsHubspotProcessMessage;
use App\Service\LoggerService;
use App\Linker\Services\HubspotQueueService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ConnectorsHubspotProcessMessageHandler
{
	private LoggerService $loggerSrv;
	private HubspotQueueService $hsQueueSrv;
	private MessageBusInterface $bus;

	public function __construct(
		HubspotQueueService $hubspotQueueSrv,
		LoggerService $loggerSrv,
		MessageBusInterface $bus,
	) {
		$this->hsQueueSrv = $hubspotQueueSrv;
		$this->loggerSrv = $loggerSrv;
		$this->bus = $bus;
	}

	public function __invoke(ConnectorsHubspotProcessMessage $message): void
	{
		$dataProcees = $message->getData();
		do {
			if (null === $dataProcees) {
				$msg = 'Hubspot data is empty.';
				$this->loggerSrv->addWarning($msg);
			}
			try {
				if (($hubspotObj = unserialize($dataProcees)) === false || !is_object($hubspotObj)) {
					throw new \Exception("Unable to unserialize payload with data $dataProcees");
				}

				$this->hsQueueSrv->processEntity(data: $hubspotObj);
				$this->loggerSrv->addInfo(sprintf('Entity name: %s ID: %s .', $hubspotObj->entityName, $hubspotObj->data['id']));

			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error processing Hubspot entity. Check logs for more details.', $thr);
				$this->enqueueDueError(object: $dataProcees);
			}
		} while (0);
	}

	private function enqueueDueError($object): void
	{
		if (is_object($object)) {
			if ($object->countFailed > 10) {
				$msg = "Hubspot Queue for entity name $object->entityName and ID {$object->data->id} exceeded the maximum of allowed  attempts.";
				$this->loggerSrv->addError($msg, [$object]);
			} else {
				++$object->countFailed;
				$id = $object?->data?->id ?? $object->data['id'] ?? 'undefined';
				$msg = "Retrying to send message to the Hubspot entity name=>$object->entityName, ID=>$id, failed=>$object->countFailed";
				$this->loggerSrv->addInfo($msg);
				try {
					$this->bus->dispatch(new ConnectorsHubspotProcessMessage(serialize($object)));
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Unable to send message for Hubspot', $thr);
				}
			}
		}
	}
}
