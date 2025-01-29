<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use App\Command\Services\UpdateMetricsService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class XtmMetricsProcess extends Command
{
	use LockableTrait;

	private UpdateMetricsService $service;

	public function __construct(
		UpdateMetricsService $updateMetricSrv
	) {
		parent::__construct();
		$this->service = $updateMetricSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:metrics:process')
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
			'<header>Fetching metrics for Analytics Projects (it may take a while)</header>',
			'<header>------------------------------------------------------------</header>',
		]);

		if (!$this->lock()) {
			$output->writeln('<entname>The command is already running in another process.</entname>');

			return 5;
		}

		return $this->service->execute($input, $output);
	}
}
