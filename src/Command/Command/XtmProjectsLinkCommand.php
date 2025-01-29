<?php

namespace App\Command\Command;

use App\Command\Services\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\LinkAnalyticsProjectsService;

class XtmProjectsLinkCommand extends Command
{
	use LockableTrait;

	private LinkAnalyticsProjectsService $service;

	public function __construct(LinkAnalyticsProjectsService $linkAnalyticProjSrv)
	{
		parent::__construct();
		$this->service = $linkAnalyticProjSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:projects:link')
			->setDescription('XTM: Links Analytic projects with regular projects')
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number of entities of single type to process during single execution of this command'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		Helper::applyFormatting($output);

		$output->writeln([
			'<header>------------------------------------------------------------</header>',
			'<header>Linking <entname>Analytics Projects</entname> with <entname>Projects</entname> and <entname>Tasks</entname> (it may take a while)</header>',
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
