<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use App\Command\Services\UpdateStatisticsService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class XtmStatisticsProcess extends Command
{
	use LockableTrait;

	private UpdateStatisticsService $service;

	public function __construct(UpdateStatisticsService $updateStatsSrv)
	{
		parent::__construct();
		$this->service = $updateStatsSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:statistics:process')
			->setDescription('Worker: Update metrics for analytics projects')
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number of entities of single type to process during single execution of this command'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$outputStyle = new OutputFormatterStyle('yellow');
		$output->getFormatter()->setStyle('entname', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green');
		$output->getFormatter()->setStyle('entval', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green', null, ['bold']);
		$output->getFormatter()->setStyle('header', $outputStyle);

		$output->writeln([
			'<header>------------------------------------------------------------</header>',
			'<header>Fetching statistics for Analytics Projects (it may take a while)</header>',
			'<header>------------------------------------------------------------</header>',
		]);

		if (!$this->lock()) {
			$output->writeln('<entname>The command is already running in another process.</entname>');

			return 5;
		}
		$this->service->execute($input, $output);

		return Command::SUCCESS;
	}
}
