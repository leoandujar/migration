<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Linker\Managers\XtmStatisticsManager;
use Symfony\Component\Console\Input\InputInterface;
use App\Model\Repository\AnalyticsProjectRepository;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStatisticsService
{
	use LockableTrait;

	private mixed $container;
	private mixed $dataManager;
	private AnalyticsProjectRepository $repository;
	private LoggerService $loggerSrv;

	public function __construct(
		XtmStatisticsManager $dataManager,
		LoggerService $loggerSrv,
		AnalyticsProjectRepository $repository
	) {
		$this->dataManager = $dataManager;
		$this->repository = $repository;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function execute(InputInterface $input, OutputInterface $output): void
	{
		$analyticsProjects = $this->repository->getIdsForStatisticsProcessing($input->getOption('limit'));
		$output->writeln('Processing '.count($analyticsProjects).' Analytics Projects Statistics');
		$this->loggerSrv->addInfo('Processing '.count($analyticsProjects).' Analytics Projects Statistics');

		foreach ($analyticsProjects as $item) {
			$output->writeln('Statistics for Analytics Project <entname>'.$item['externalId'].'</entname>: ');
			$this->loggerSrv->addInfo('Statistics for Analytics Project '.$item['externalId']);

			$results = $this->dataManager->updateProjectStatistics($item['externalId']);

			foreach ($results as $lang => $result) {
				$output->write('                                <entname>'.$lang.'</entname>: ');
				$result = Helper::resultToString($result);
				$output->writeln('<entval>'.$result.'</entval>');
				$this->loggerSrv->addInfo($lang.': '.$result);
			}
		}
	}
}
