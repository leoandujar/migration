<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use App\Linker\Services\QboService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsQuickbooksProcessCommand extends Command
{
	use LockableTrait;

	private const LIMIT_TO_PROCESS = 50;

	private QboService $qboService;
	private LoggerService $loggerSrv;

	public function __construct(QboService $qboService, LoggerService $loggerSrv)
	{
		parent::__construct();
		$this->qboService = $qboService;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:quickbooks:process')
			->setDescription('Qbo: Process the queue with QBO changes.')
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
		$output->writeln('<entname>Entering to process QBO Queue.</entname>');

		try {
			$this->qboService->processQueueWebhook(intval($input->getOption('limit')));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error processing qbo-queue.', $thr);
		}

		return Command::SUCCESS;
	}
}
