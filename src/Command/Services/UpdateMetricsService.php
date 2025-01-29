<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use App\Linker\Managers\XtmMetricsManager;
use Symfony\Component\Console\Input\InputInterface;
use App\Model\Repository\AnalyticsProjectRepository;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMetricsService
{
	private mixed $dataManager;
	private AnalyticsProjectRepository $repository;
	private LoggerService $loggerSrv;

	public function __construct(
		XtmMetricsManager $dataManager,
		LoggerService $loggerSrv,
		AnalyticsProjectRepository $repository
	) {
		$this->dataManager = $dataManager;
		$this->repository = $repository;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	/**
	 * @return int
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$analyticsProjects = $this->repository->getIdsForMetricsProcessing($input->getOption('limit'));
		$output->writeln('Processing '.count($analyticsProjects).' Analytics Projects Metrics');

		foreach ($analyticsProjects as $item) {
			$output->writeln('Metrics for Analytics Project <entname>'.$item['externalId'].'</entname>: ');
			$this->loggerSrv->addInfo('Metrics for Analytics Project '.$item['externalId']);

			$results = $this->dataManager->updateProjectMetrics($item['externalId']);
			if (!is_array($results)) {
				$output->writeln('<error>Error! Metrics update failed</error>');
				$this->loggerSrv->addInfo('Error! Metrics update failed: '.$item['externalId']);
				continue;
			}

			foreach ($results as $lang => $result) {
				$output->write('                                <entname>'.$lang.'</entname>: ');
				$result = Helper::resultToString($result);
				$output->writeln('<entval>'.$result.'</entval>');
				$this->loggerSrv->addInfo('Metrics for lang:  '.$lang.': '.$result);
			}
		}

		return Command::SUCCESS;
	}
}
