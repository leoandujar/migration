<?php

namespace App\MessageHandler;

use App\Command\Services\Helper;
use App\Linker\Managers\XtmMetricsManager;
use App\Message\XtmMetricsProcessMessage;
use App\Model\Entity\AnalyticsProject;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmMetricsProcessMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private mixed $dataManager;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		XtmMetricsManager $dataManager,
	) {
		$this->em = $em;
		$this->dataManager = $dataManager;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function __invoke(XtmMetricsProcessMessage $message): void
	{
		$limit = $message->getLimit();
		$this->loggerSrv->addInfo('Fetching metrics for Analytics Projects (it may take a while)');

		$analyticsProjects = $this->em->getRepository(AnalyticsProject::class)->getIdsForMetricsProcessing($limit);
		$this->loggerSrv->addInfo('Processing '.count($analyticsProjects).' Analytics Projects Metrics');

		foreach ($analyticsProjects as $item) {
			$this->loggerSrv->addInfo('Metrics for Analytics Project '.$item['externalId']);

			$results = $this->dataManager->updateProjectMetrics($item['externalId']);
			if (!is_array($results)) {
				$this->loggerSrv->addInfo('Error! Metrics update failed: '.$item['externalId']);
				continue;
			}

			foreach ($results as $lang => $result) {
				$result = Helper::resultToString($result);
				$this->loggerSrv->addInfo('Metrics for lang:  '.$lang.': '.$result);
			}
		}
	}
}
