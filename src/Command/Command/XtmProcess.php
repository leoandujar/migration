<?php

namespace App\Command\Command;

use App\Command\Services\UpdateAllService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XtmProcess extends Command
{
	private UpdateAllService $service;

	public function __construct(
		UpdateAllService $updateAllSrv,
	) {
		parent::__construct();
		$this->service = $updateAllSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:process')
			->setDescription('Worker: Fetch a list of IDs of entities to update since last update')
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Force to run even if the command was never executed before'
			)
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_OPTIONAL,
				'Maximum number of entities of single type to process during single execution of this command'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->service->execute($input, $output);
		$output->writeln('');

		return Command::SUCCESS;
	}
}
