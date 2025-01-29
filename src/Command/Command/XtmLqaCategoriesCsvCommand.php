<?php

namespace App\Command\Command;

use App\Command\Services\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\InitializeLqaIssueTypeService;

class XtmLqaCategoriesCsvCommand extends Command
{
	use LockableTrait;

	private InitializeLqaIssueTypeService $service;

	public function __construct(InitializeLqaIssueTypeService $initializeLqaIssueTypeSrv)
	{
		parent::__construct();
		$this->service = $initializeLqaIssueTypeSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:lqa:categories:csv')
			->setDescription('Worker: Populate LQA Issue Types in DB');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		Helper::applyFormatting($output);

		$output->writeln([
			'<header>------------------------------------------------------------</header>',
			'<header>Initializing <entval>Branches</entval></header>',
			'<header>------------------------------------------------------------</header>',
		]);

		if (!$this->lock()) {
			$output->writeln('<entname>The command is already running in another process.</entname>');

			return 5;
		}

		$this->service->execute($output);

		return Command::SUCCESS;
	}
}
