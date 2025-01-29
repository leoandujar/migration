<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\UpdateAnalyticsProjectsService;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class XtmProjectsUpdateCommand extends Command
{
	use LockableTrait;

	private UpdateAnalyticsProjectsService $service;

	/**
	 * XtmProjectsUpdateCommand constructor.
	 */
	public function __construct(UpdateAnalyticsProjectsService $updateAnalyticProSrv)
	{
		parent::__construct();
		$this->service = $updateAnalyticProSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:projects:update')
			->setDescription('Worker: Updates Analytics Projects list in Database using XTM API.')
			->addOption(
				'start',
				's',
				InputOption::VALUE_REQUIRED,
				'Number of page to begin with (skip lower)',
				'1'
			)
			->addOption(
				'finishedDate',
				'f',
				InputOption::VALUE_OPTIONAL,
				'Date of finished project',
				''
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
			'<header>Checking and updating <entname>Analytics Projects</entname> (it may take a while)</header>',
			'<header>------------------------------------------------------------</header>',
		]);

		if (!$this->lock()) {
			$output->writeln('<entname>The command is already running in another process.</entname>');

			return Command::SUCCESS;
		}

		$this->service->execute($input, $output);

		return Command::SUCCESS;
	}
}
