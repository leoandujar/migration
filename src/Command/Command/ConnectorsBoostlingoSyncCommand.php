<?php

namespace App\Command\Command;

use App\Command\Services\BoostlingoRetrieveClientService;
use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsBoostlingoSyncCommand extends Command
{
	private LoggerService $loggerSrv;
	private BoostlingoRetrieveClientService $blRetriveClientSrv;

	public function __construct(
		BoostlingoRetrieveClientService $blRetriveClientSrv,
		LoggerService $loggerSrv
	) {
		parent::__construct();
		$this->loggerSrv = $loggerSrv;
		$this->blRetriveClientSrv = $blRetriveClientSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:boostlingo:sync')
			->setDescription('Boostlingo: Fetch the clients for getting the xtrf customer id.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('Collecting clients to process.');
		$this->blRetriveClientSrv->processClients($output);

		return Command::SUCCESS;
	}
}
