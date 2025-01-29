<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\PostmarkProcessQueueService;

class ConnectorsPostmarkProcessCommand extends Command
{
	private const LIMIT_TO_PROCESS = 50;

	private PostmarkProcessQueueService $postmarkQueueSrv;

	public function __construct(
		PostmarkProcessQueueService $postmarkQueueSrv
	) {
		parent::__construct();
		$this->postmarkQueueSrv = $postmarkQueueSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:postmark:process')
			->setDescription('Postmark: Process specific number of rows in Postmark Queue. This command allows you to process {limit} of rows in the Postmark Queue.')
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number to fetch from the queue.',
				self::LIMIT_TO_PROCESS
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$limit = $input->getOption('limit');
		$this->postmarkQueueSrv->dequeueAndProcess($output, $limit);

		return Command::SUCCESS;
	}
}
