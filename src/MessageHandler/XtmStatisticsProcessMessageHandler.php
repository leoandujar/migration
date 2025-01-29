<?php

namespace App\MessageHandler;

use App\Command\Services\Helper;
use App\Linker\Managers\XtmStatisticsManager;
use App\Message\XtmStatisticsProcessMessage;
use App\Model\Entity\AnalyticsProject;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmStatisticsProcessMessageHandler
{
	private EntityManagerInterface $em;
	private mixed $dataManager;
	private LoggerService $loggerSrv;

	public function __construct(
		XtmStatisticsManager $dataManager,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
	) {
		$this->dataManager = $dataManager;
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
	}

	public function __invoke(XtmStatisticsProcessMessage $message): void
	{
		$limit = $message->getLimit();
		$analyticsProjects = $this->em->getRepository(AnalyticsProject::class)->getIdsForStatisticsProcessing($limit);
		$this->loggerSrv->addInfo('Processing '.count($analyticsProjects).' Analytics Projects Statistics');

		foreach ($analyticsProjects as $item) {
			$this->loggerSrv->addInfo('Processing '.$item.' Analytics Projects');
			$this->loggerSrv->addInfo('Statistics for Analytics Project '.$item['externalId']);
			$results = $this->dataManager->updateProjectStatistics($item['externalId']);
			foreach ($results as $lang => $result) {
				$result = Helper::resultToString($result);
				$this->loggerSrv->addInfo($lang.': '.$result);
			}
		}
	}
}
