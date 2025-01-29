<?php

namespace App\Command\Command;

use App\Linker\Services\RedisClients;
use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\ProjectQuotesProcessQueueService;

class CustomerportalFilesProjectsDequeueCommand extends Command
{
	use LockableTrait;

	private LoggerService $loggerSrv;
	private ProjectQuotesProcessQueueService $projectQuotesProcessSrv;

	public function __construct(
		LoggerService $loggerSrv,
		ProjectQuotesProcessQueueService $projectQuotesProcessSrv,
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
		$this->projectQuotesProcessSrv = $projectQuotesProcessSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('customerportal:files:projects:dequeue')
			->setDescription('Project-Quote: This commands process all project or quotes pending into the queue.')
			->addOption(
				name: 'queue_name',
				shortcut: 'qn',
				mode: InputOption::VALUE_OPTIONAL,
				default: RedisClients::SESSION_KEY_PROJECTS_QUOTES
			)
			->addOption(
				name: 'limit',
				shortcut: 'l',
				mode: InputOption::VALUE_OPTIONAL,
				default: 10
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$queueName = $input->getOption('queue_name') ?: null;
		$limit = $input->getOption('limit') ?: 10;

		if (!$queueName) {
			$this->loggerSrv->addError('Queue name is empty. Aborting.');

			return Command::FAILURE;
		}
		$this->projectQuotesProcessSrv->dequeueAndProcess($output, $queueName, $limit);

		return Command::SUCCESS;
	}
}
