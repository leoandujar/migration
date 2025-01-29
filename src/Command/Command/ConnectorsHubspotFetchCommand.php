<?php

namespace App\Command\Command;

use App\Linker\Services\HubspotQueueService;
use App\Service\LoggerService;
use App\Command\Services\HubspotFetchService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsHubspotFetchCommand extends Command
{
	private LoggerService $loggerSrv;
	private HubspotFetchService $hsFetchSrv;

	public function __construct(
		HubspotFetchService $updateHubspotSrv,
		LoggerService $loggerSrv,
	) {
		parent::__construct();
		$this->hsFetchSrv = $updateHubspotSrv;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:hubspot:fetch')
			->setDescription('Hubspot: This command allows you to update all entities from Hubspot or specific one depending the parameter defined.')
			->addOption(
				'entity',
				'en',
				InputOption::VALUE_OPTIONAL,
				'Name of the entity to be updated. One of Account, Item, Vendor, Invoice, InvoiceItems, Payment, Bill, Vendor, BillPayment'
			)
			->addOption(
				'id',
				'id',
				InputOption::VALUE_OPTIONAL,
				'Id of the specific entity to be updated.'
			)
			->addOption(
				'only_dequeue',
				'only_dequeue',
				InputOption::VALUE_REQUIRED,
				'Id of the specific entity to be updated.',
				0
			)
			->addOption(
				'update_remote',
				'update_remote',
				InputOption::VALUE_REQUIRED,
				'Id of the specific entity to be updated.',
				0
			)
			->addOption(
				'dequeue_limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number to fetch from the queue.',
				3000
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$entityId = $input->getOption('id');
		$targetEntity = $input->getOption('entity');
		$dequeueLimit = $input->getOption('dequeue_limit');
		$onlyDequeue = intval($input->getOption('only_dequeue'));
		$updateRemote = intval($input->getOption('update_remote'));
		if (!empty($entityId) && empty($targetEntity)) {
			$msg = 'You provided entity ID but not entity name.';
			$this->loggerSrv->addError("HubspotService=> $msg");
			$output->writeln($msg);

			return Command::FAILURE;
		}

		$entitiesNames = HubspotFetchService::ENTITIES;
		if (null !== $targetEntity) {
			if (in_array(strtolower($targetEntity), $entitiesNames)) {
				$entitiesNames = [strtolower($targetEntity)];
			} else {
				$output->writeln("The entity name $targetEntity does not exists in our list.");

				return Command::FAILURE;
			}
		}

		$output->writeln('Collecting entities to process.');
		foreach ($entitiesNames as $entityName) {
			$output->writeln("Starting processing for entity $entityName");
			/**
			 * In case that $responseObject has value, it means that command was called with params --id
			 * When this happens we dont need to enqueue the object and fetch the queue again.
			 * Only we need to directly process the $object fetched from Hubspot without enqueue and dequeue.
			 * In case that we enqueue, we lost the reference to the object and we need to iterate all objects into
			 * the redis queue for finding the specific one.
			 */
			$responseObject = null;
			if (1 !== $onlyDequeue && 1 !== $updateRemote) {
				$responseObject = $this->hsFetchSrv->fetchAndEnqueue($entityName, $output, $entityId);
			}
			if (1 === $updateRemote) {
				$this->hsFetchSrv->updateRemote($entityName, $output);
			}
			if (false !== $responseObject) {
				$responseObject = (object) [
					'countFailed' => 0,
					'entityName' => $entityName,
					'operation' => HubspotQueueService::OPERATION_CREATE_OR_UPDATE,
					'data' => $responseObject,
				];
				$this->hsFetchSrv->dequeueAndProcess($responseObject, $output, $dequeueLimit);
			}
		}

		return Command::SUCCESS;
	}
}
