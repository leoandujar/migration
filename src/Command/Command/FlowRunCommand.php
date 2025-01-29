<?php

namespace App\Command\Command;

use App\Message\FlowRunMessage;
use App\Model\Entity\AVWorkflowMonitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class FlowRunCommand extends Command
{
	private EntityManagerInterface $em;
	private MessageBusInterface $bus;

	public function __construct(
		EntityManagerInterface $em,
		MessageBusInterface $bus,
	) {
		parent::__construct();
		$this->em = $em;
		$this->bus = $bus;
	}

	protected function configure(): void
	{
		$this
			->setName('run:flow')
			->setDescription('Command to run flow (new feature for Avantus Project)')
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
	 * @throws \Throwable
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$monitorId = $input->getArgument('monitor_id');

		$flowId = $this->em->getRepository(AVWorkflowMonitor::class)->find($monitorId)->getDetails()['params']['flow_id'];

		$this->bus->dispatch(new FlowRunMessage($monitorId, $flowId));

		return Command::SUCCESS;
	}
}
