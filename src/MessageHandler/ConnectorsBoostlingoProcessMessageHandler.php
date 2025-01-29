<?php

namespace App\MessageHandler;

use App\Linker\Services\BoostlingoQueueService;
use App\Message\ConnectorsBoostlingoProcessMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ConnectorsBoostlingoProcessMessageHandler
{
	private LoggerService $loggerSrv;
	private BoostlingoQueueService $boostlingoQueueService;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		BoostlingoQueueService $boostlingoQueueService,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->boostlingoQueueService = $boostlingoQueueService;
		$this->bus = $bus;
	}

	public function __invoke(ConnectorsBoostlingoProcessMessage $message): void
	{
		$dataProcees = $message->getData();
		do {
			if (null === $dataProcees) {
				$msg = 'Boostlingo data is empty.';
				$this->loggerSrv->addWarning($msg);
			}
			if (($boostLingoObj = unserialize($dataProcees)) === false || !is_object($boostLingoObj)) {
				throw new \Exception("Unable to unserialize payload with data $dataProcees");
			}
			try {
				$this->boostlingoQueueService->processEntity($boostLingoObj);
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error processing Boostlingo entity. Check logs for more details.', $thr);
				$this->enqueueDueError($boostLingoObj);
			}
		} while (0);
	}

	private function enqueueDueError(mixed $object): void
	{
		$id = $object?->data->id ?? $object->data['id'] ?? $object->data['data']['id'] ?? null;
		if ($object->countFailed > 10) {
			$msg = "Error processing Boostlingo entity $object->entityName with ID $id, exceeded the maximum of allowed  attempts.";
			$this->loggerSrv->addError($msg, [$object]);
		} else {
			++$object->countFailed;
			$msg = "Retrying to send message to Boostlingo entity name=>$object->entityName, ID=>$id, failed=>$object->countFailed";
			$this->loggerSrv->addInfo($msg);
			try {
				$this->bus->dispatch(new ConnectorsBoostlingoProcessMessage(serialize($object)));
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error processing Boostlingo entity.', $thr);
			}
		}
	}
}
