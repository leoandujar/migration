<?php

namespace App\MessageHandler;

use App\Connector\Xtm\Response\ProjectsCountResponse;
use App\Connector\Xtm\XtmConnector;
use App\Message\XtmProjectsUpdateMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmProjectsUpdateMessageHandler
{
	private LoggerService $loggerSrv;
	private XtmConnector $xtmConnector;

	public function __construct(
		LoggerService $loggerSrv,
		XtmConnector $xtmConnector,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->xtmConnector = $xtmConnector;
	}

	public function __invoke(XtmProjectsUpdateMessage $message): void
	{
		$finishedDate = $message->getFinishedDate();
		$finishedDateFrom = null;
		if (!empty($finishedDate)) {
			$finishedDateFrom = $finishedDate;
		}

		/** @var ProjectsCountResponse $response */
		$response = $this->xtmConnector->projectsCount($finishedDateFrom);
		$pages = 0;
		if (null !== $response) {
			$pages = $response->getTotalPages();
		}

		$this->loggerSrv->addInfo('pages to process: '.$pages);
		$this->loggerSrv->addInfo('Analytics Projects: pages to process: '.$pages);

	}
}
