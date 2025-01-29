<?php

namespace App\Command\Command;

use App\Command\Services\RulesQueueService;
use Symfony\Component\Console\Command\LockableTrait;
use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerportalRulesDequeueCommand extends Command
{
	use LockableTrait;

	private LoggerService $loggerSrv;
	private RulesQueueService $rulesQueueSrv;

	public function __construct(
		LoggerService $loggerSrv,
		RulesQueueService $rulesQueueSrv
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->rulesQueueSrv = $rulesQueueSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('customerportal:rules:process');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		if (!$this->lock()) {
			$output->writeln('The command is already running in another process.');

			return Command::SUCCESS;
		}

		$this->rulesQueueSrv->dequeueAndProcess($output);

		return Command::SUCCESS;
	}
}
