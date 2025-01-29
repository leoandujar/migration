<?php

namespace App\Command\Command;

use App\Command\Services\TriggerSyncService;
use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReplicationTriggersSyncCommand extends Command
{
	private LoggerService $loggerSrv;
	private TriggerSyncService $triggerSyncSrv;

	public function __construct(
		TriggerSyncService $triggerSyncSrv,
		LoggerService $loggerSrv
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->triggerSyncSrv = $triggerSyncSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('replication:triggers:sync')
			->setDescription('This command execute a set of query for sync the trigger on db.')
			->addOption(
				'name',
				'name',
				InputOption::VALUE_REQUIRED,
				'The sync function name to run.'
			)
			->addOption(
				'start',
				'start',
				InputOption::VALUE_REQUIRED,
				'The offset of the query result.'
			)
			->addOption(
				'per_page',
				'per_page',
				InputOption::VALUE_REQUIRED,
				'The number of values in the query result.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$name = $input->getOption('name');
		$start = $input->getOption('start');
		$perPage = $input->getOption('per_page');
		$this->triggerSyncSrv->syncTrigger($output, $name, $start, $perPage);

		return Command::SUCCESS;
	}
}
