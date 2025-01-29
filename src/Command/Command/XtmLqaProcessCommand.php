<?php

namespace App\Command\Command;

use App\Command\Services\Helper;
use App\Command\Services\UpdateLqaService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class XtmLqaProcessCommand extends Command
{
	use LockableTrait;

	private UpdateLqaService $service;

	/**
	 * XtmLqaProcessCommand constructor.
	 */
	public function __construct(
		UpdateLqaService $updateLqaSrv
	) {
		setlocale(LC_ALL, 'us_US.UTF8');
		parent::__construct();
		$this->service = $updateLqaSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:lqa:process')
			->setDescription('Worker: Process LQA files')
			->addOption(
				'start',
				's',
				InputOption::VALUE_REQUIRED,
				'ID of entity to begin with (skip lower)',
				'0'
			)
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Fetch just l entries',
				'100'
			)
			->addOption(
				'one',
				null,
				InputOption::VALUE_REQUIRED,
				'Number of external ID'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		Helper::applyFormatting($output);

		$output->writeln([
			'Fetching and processing <entname>LQA reports</entname> of finished analytics projects',
			'',
		]);

		if (!$this->lock()) {
			$output->writeln('<warning>The command is already running in another process.</warning>');

			return 5;
		}

		$this->service->execute($input, $output);

		return Command::SUCCESS;
	}
}
