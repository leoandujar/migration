<?php

namespace App\Command\Command;

use App\Command\Services\Helper;
use App\Command\Services\LinkJobsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class XtmJobsLinkCommand extends Command
{
	use LockableTrait;

	private LinkJobsService $service;

	public function __construct(
		LinkJobsService $linkJobSrv
	) {
		parent::__construct();
		$this->service = $linkJobSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:jobs:link')
			->setDescription('Worker: Links Jobs with Analytic Projects')
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number of entities of single type to process during single execution of this command',
				100
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		Helper::applyFormatting($output);

		$output->writeln([
			'<header>------------------------------------------------------------</header>',
			'<header>Linking <entname>Jobs</entname> with <entname>Analytics Projects</entname> (it may take a while)</header>',
			'<header>------------------------------------------------------------</header>',
		]);

		if (!$this->lock()) {
			$output->writeln('<warning>The command is already running in another process.</warning>');

			return 5;
		}
		$this->service->execute($input, $output);

		return Command::SUCCESS;
	}
}
