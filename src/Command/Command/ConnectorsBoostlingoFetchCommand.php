<?php

namespace App\Command\Command;

use App\Command\Services\BoostlingoFetchService;
use App\Command\Services\BoostlingoInvoiceCallService;
use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsBoostlingoFetchCommand extends Command
{
	private LoggerService $loggerSrv;
	private BoostlingoFetchService $boostlingoFetchService;
	private BoostlingoInvoiceCallService $boostlingoInvoiceCallService;

	public function __construct(
		BoostlingoFetchService $boostlingoFetchService,
		BoostlingoInvoiceCallService $boostlingoInvoiceCallService,
		LoggerService $loggerSrv,
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->boostlingoFetchService = $boostlingoFetchService;
		$this->boostlingoInvoiceCallService = $boostlingoInvoiceCallService;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:boostlingo:fetch')
			->setDescription('Boostlingo: Fetch data from remote server. This command allows you to update all entities from Boostlingo or specific one depending the parameter defined.')
			->addOption(
				'entity',
				'en',
				InputOption::VALUE_OPTIONAL,
				'Name of the entity to be updated. One of Contact, Customer, Call, Dictionary'
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
				InputOption::VALUE_OPTIONAL,
				'Id of the specific entity to be updated.'
			)
			->addOption(
				'start_date',
				'start_date',
				InputOption::VALUE_OPTIONAL,
				'The start date for filtering. If defined also end_date need to be present.'
			)
			->addOption(
				'end_date',
				'end_date',
				InputOption::VALUE_OPTIONAL,
				'The end date for filtering. If defined also start_date need to be present.'
			)
			->addOption(
				'since',
				'since',
				InputOption::VALUE_OPTIONAL,
				'It is used to define a start time and an end time from the value passed as a parameter and using the current day as the end date.'
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
		$startDate = $input->getOption('start_date');
		$endDate = $input->getOption('end_date');
		$since = $input->getOption('since');
		$dequeueLimit = $input->getOption('dequeue_limit');
		$onlyDequeue = intval($input->getOption('only_dequeue'));
		if (!empty($entityId) && empty($targetEntity)) {
			$msg = 'You provided entity ID but not entity name.';
			$this->loggerSrv->addError("BoostlingoService=> $msg");
			$output->writeln($msg);

			return Command::FAILURE;
		}
		if ($since) {
			$endDate = (new \DateTime())->format('Y-m-d\TH:i:s.u\Z');
			$startDate = (new \DateTime())->modify("- $since")->format('Y-m-d\TH:i:s.u\Z');
		}
		if (($startDate xor $endDate) && !$since) {
			$msg = 'You need to provide both start_date and end_date';
			$this->loggerSrv->addError("BoostlingoService=> $msg");
			$output->writeln($msg);

			return Command::FAILURE;
		}
		if ($startDate && $endDate) {
			$startDate = (new \DateTime($startDate))->format('Y-m-d\TH:i:s.u\Z');
			$endDate = (new \DateTime($endDate))->format('Y-m-d\TH:i:s.u\Z');
		}

		$entitiesNames = BoostlingoFetchService::ENTITIES;
		if (!empty($targetEntity)) {
			if (in_array(strtolower($targetEntity), $entitiesNames)) {
				$entitiesNames = [strtolower($targetEntity)];
			} else {
				$output->writeln("The entity name $targetEntity does not exists in our list.");

				return Command::FAILURE;
			}
		}

		if ('invoices_calls' === $targetEntity) {
			$this->boostlingoInvoiceCallService->processEntities($output);

			return Command::SUCCESS;
		}

		$output->writeln('Collecting entities to process.');
		foreach ($entitiesNames as $entityName) {
			$output->writeln("Starting processing for entity $entityName");
			/**
			 * In case that $responseObject has value, it means that command was called with params --id
			 * When this happens we don't need to enqueue the object and fetch the queue again.
			 * Only we need to directly process the $object fetched from Hubspot without enqueue and dequeue.
			 * In case that we enqueue, we lost the reference to the object and we need to iterate all objects into
			 * the redis queue for finding the specific one.
			 */
			$responseObject = null;
			if (1 !== $onlyDequeue) {
				$responseObject = $this->boostlingoFetchService->fetchAndEnqueue($entityName, $output, $startDate, $endDate, $entityId);
			}
			if (false !== $responseObject) {
				$this->boostlingoFetchService->dequeueAndProcess(null, $output, $dequeueLimit);
			}
		}

		return Command::SUCCESS;
	}
}
