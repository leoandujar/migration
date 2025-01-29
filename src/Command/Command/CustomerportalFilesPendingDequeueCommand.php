<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use App\Command\Services\FilesQueueService;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerportalFilesPendingDequeueCommand extends Command
{
	use LockableTrait;

	private LoggerService $loggerSrv;
	private FilesQueueService $filesProcessSrv;

	public function __construct(
		LoggerService $loggerSrv,
		FilesQueueService $filesProcessSrv,
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
		$this->filesProcessSrv = $filesProcessSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('customerportal:files:pending:dequeue')
			->setDescription('Files: process all file into the queue.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		if (!$this->lock()) {
			$output->writeln('The command is already running in another process.');

			return Command::SUCCESS;
		}

		$this->filesProcessSrv->dequeueAndProcess($output);

		return Command::SUCCESS;
	}
}
