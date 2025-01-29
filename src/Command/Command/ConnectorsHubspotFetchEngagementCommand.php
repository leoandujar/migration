<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use SevenShores\Hubspot\Http\Response;
use App\Connector\Hubspot\HubspotConnector;
use App\Linker\Services\HubspotQueueService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsHubspotFetchEngagementCommand extends Command
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private HubspotConnector $hsConnector;

	public function __construct(
		RedisClients $redisClients,
		HubspotConnector $hsConnector,
		LoggerService $loggerSrv
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->hsConnector = $hsConnector;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('connectos:hubspot:fetch:engagement')
			->setDescription('Hubspot: Synchronize the entities type Engagement from remote server.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$entityName = HubspotQueueService::ENTITY_NAME_ENGAGEMENTS;
		$output->writeln('Collecting entities to process.');
		$output->writeln(sprintf('COLLECTING ENTITIES OF TYPE "%s"', $entityName));
		$totalProcessed = 0;

		try {
			$this->processEntity($output, $totalProcessed);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing the Hubspot entity $entityName.", $thr);
			$output->writeln($thr->getMessage());
		}
		$output->writeln(sprintf('PROCESSED %s ROWS FOR ENTITY %s', $totalProcessed, $entityName));

		return Command::SUCCESS;
	}

	private function processEntity(OutputInterface $output, &$totalProcessed): void
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
				$output->writeln($msg);
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
				$output->writeln(sprintf('Adding %d OF %d %s ID: %s to the queue.', $totalProcessed, $total, $entityName, $objectId));
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
