<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use App\Command\Services\HubspotProcessQueueService;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsHubspotProcessCommand extends Command
{
	private const LIMIT_TO_PROCESS = 50;

	private HubspotProcessQueueService $hubspotQueueSrv;

	public function __construct(
		HubspotProcessQueueService $hubspotQueueSrv,
	) {
		parent::__construct();
		$this->hubspotQueueSrv = $hubspotQueueSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('hubspot:process-queue')
			->setDescription('Hubspot: Process specific number of rows in Hubspot Queue. This command allows you to process {limit} of rows in the Hubspot Queue.')
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number to fetch from the queue.',
				self::LIMIT_TO_PROCESS
			)
			->addOption(
				'queue',
				'qq',
				InputOption::VALUE_OPTIONAL,
				'queue to process.',
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$limit = $input->getOption('limit');
		$queue = $input->getOption('queue') ?? null;
		$this->hubspotQueueSrv->dequeueAndProcess($output, $limit, $queue);

		return Command::SUCCESS;
	}
}
