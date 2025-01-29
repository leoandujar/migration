<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use App\Linker\Services\HubspotQueueService;
use Symfony\Component\Console\Output\OutputInterface;

class HubspotProcessQueueService
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private HubspotQueueService $hsQueueSrv;

	public function __construct(
		LoggerService $loggerSrv,
		HubspotQueueService $hsQueueSrv,
		RedisClients $redisClients
	) {
		$this->hsQueueSrv = $hsQueueSrv;
		$this->redisClients = $redisClients;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function dequeueAndProcess(OutputInterface $output, ?int $dequeueLimit = 10000, string $queue = null): void
	{
		do {
			$queue = $queue ?? RedisClients::SESSION_KEY_HUBSPOT_WEBHOOK_QUEUE;
			$totalProcessed = 0;
			$output->writeln('PROCESSING HUBSPOT QUEUE.');
			if (RedisClients::SESSION_KEY_HUBSPOT_WEBHOOK_QUEUE !== $queue && RedisClients::SESSION_KEY_HUBSPOT_COMMAND_QUEUE !== $queue) {
				$msg = 'The selected queue is not from Hubspot. Please select a valid queue.';
				$this->loggerSrv->addWarning($msg);
				$output->writeln($msg);
				$dequeueLimit = 0;
			}
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop($queue)) !== null) {
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

					$processedResponse = $this->hsQueueSrv->processEntity($hubspotObj);
					++$totalProcessed;
					if (true !== $processedResponse) {
						--$totalProcessed;
						$this->enqueueDueError($hubspotObj, $output);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Hubspot entity. Check logs for more details.', $thr);
					$this->enqueueDueError($payload, $output);
					--$totalProcessed;
					continue;
				}
			}
		} while (0);
		$output->writeln(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));
	}

	private function enqueueDueError($object, OutputInterface $output): void
	{
		if (is_object($object)) {
			if ($object->countFailed > RedisClients::DEFAULT_QUEUE_COUNT_FAILURE) {
				$msg = "Hubspot Queue for entity name $object->entityName and ID {$object->data->id} exceeded the maximum of allowed  attempts. It will not be added to the queue";
				$this->loggerSrv->addError($msg, [$object]);
				$output->writeln($msg);
			} else {
				++$object->countFailed;
				$id = $object?->data?->id ?? $object->data['id'] ?? 'undefined';
				$msg = "Adding again to queue the Hubspot entity name=>$object->entityName, ID=>$id, failed=>$object->countFailed";
				$this->loggerSrv->addInfo($msg);
				$output->writeln($msg);
				$position = $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_HUBSPOT_WEBHOOK_QUEUE, serialize($object));
				if ($position < 0) {
					$this->loggerSrv->addError('Unable to Enqueue the payload for Hubspot', [
						'Entity name' => $object->entityName,
						'ID' => $id,
						'data' => $object,
					]);
					$output->writeln("Unable to enqueue again the Hubspot entity $object->entityName and ID $id");
				}
			}
		}
	}
}
