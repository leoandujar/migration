<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\UpdateAnalyticsProjectsPageService;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class XtmProjectsUpdatePageCommand extends Command
{
	use LockableTrait;

	public $hidden = true;
	private mixed $logger;
	private mixed $entityManager;
	private UpdateAnalyticsProjectsPageService $service;

	public function __construct(UpdateAnalyticsProjectsPageService $service)
	{
		parent::__construct();
		$this->service = $service;
	}

	protected function configure(): void
	{
		$this
			->setName('xtm:projects:update:page')
			->setDescription('Worker: Updates a page of Analytics Projects')
			->addOption(
				'page',
				'p',
				InputOption::VALUE_REQUIRED,
				'Number of page to process',
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
			'<header>Checking and updating <entname>Analytics Projects</entname> per page (it may take a while)</header>',
			'<header>------------------------------------------------------------</header>',
		]);

		if (!$this->lock()) {
			$output->writeln('<entname>The command is already running in another process.</entname>');

			return 5;
		}

		return $this->service->execute($input, $output);
	}
}
