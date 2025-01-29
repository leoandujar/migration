<?php

namespace App\Command\Command;

use App\Message\WorkflowRunMessage;
use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class WorkflowRunMessengerCommand extends Command
{
	public bool $hidden = true;
	private LoggerService $loggerSrv;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		MessageBusInterface $bus,
		?string $name = null,
	) {
		parent::__construct($name);
		$this->loggerSrv = $loggerSrv;
		$this->bus = $bus;
	}

	protected function configure(): void
	{
		$this
			->setName('workflow:run:copy')
			->setDescription('Run workflow using messenger component.')
			->addArgument(
				'service_name',
				InputArgument::REQUIRED,
				'The service name of the workflow (ex workflow.test)'
			)
			->addArgument(
				'monitor_id',
				InputArgument::OPTIONAL,
				'The workflow monitor id related to this instance.'
			);
	}

	/**
	 * @throws \Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$name = $input->getArgument('service_name');
		$monitorId = $input->getArgument('monitor_id');

		try {
			$this->bus->dispatch(new WorkflowRunMessage($name, $monitorId));

			$output->writeln('Workflow run message dispatched.');

			return Command::SUCCESS;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError($thr->getMessage());

			return Command::FAILURE;
		}
	}
}
