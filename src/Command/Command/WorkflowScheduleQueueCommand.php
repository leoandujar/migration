<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\WorkflowAutoProcessQueueService;

class WorkflowScheduleQueueCommand extends Command
{
	use LockableTrait;

	private LoggerService $loggerSrv;
	private WorkflowAutoProcessQueueService $wfautoProcessSrv;

	public function __construct(
		LoggerService $loggerSrv,
		WorkflowAutoProcessQueueService $wfautoProcessSrv,
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->wfautoProcessSrv = $wfautoProcessSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('workflow:schedule:queue')
			->setDescription('Workflow: Check the workflows that run automatically and put them into the queue. They are added to workflow queue according with the time set in workflow cron.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		if (!$this->lock()) {
			$output->writeln('The command is already running in another process.');

			return Command::SUCCESS;
		}

		$this->wfautoProcessSrv->dequeueAndProcess($output);

		return Command::SUCCESS;
	}
}
