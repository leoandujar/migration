<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\WorkflowProcessQueueService;

class WorkflowScheduleRunCommand extends Command
{
	use LockableTrait;

	private LoggerService $loggerSrv;
	private WorkflowProcessQueueService $wfProcessSrv;

	public function __construct(
		LoggerService $loggerSrv,
		WorkflowProcessQueueService $wfProcessSrv,
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->wfProcessSrv = $wfProcessSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('workflow:schedule:run')
			->setDescription('Workflow: Run the workflows into the queue.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		if (!$this->lock()) {
			$output->writeln('The command is already running in another process.');

			return Command::SUCCESS;
		}

		$this->wfProcessSrv->dequeueAndProcess($output);

		return Command::SUCCESS;
	}
}
