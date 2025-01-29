<?php

namespace App\MessageHandler;

use App\Message\XtmJobsLinkMessage;
use App\Message\XtmLqaProcessMessage;
use App\Message\XtmMetricsProcessMessage;
use App\Message\XtmProcessMessage;
use App\Message\XtmProjectExtendedMessage;
use App\Message\XtmProjectsLinkMessage;
use App\Message\XtmStatisticsProcessMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class XtmProcessMessageHandler
{
	private MessageBusInterface $bus;
	private LoggerService $loggerSrv;

	public function __construct(
		MessageBusInterface $bus,
		LoggerService $loggerSrv,
	) {
		$this->bus = $bus;
		$this->loggerSrv = $loggerSrv;
	}

	public function __invoke(XtmProcessMessage $message): void
	{
		$limit = $message->getLimit();

		try {
			$this->bus->dispatch(new XtmProjectsLinkMessage($limit));
			$this->bus->dispatch(new XtmJobsLinkMessage($limit));
			$this->bus->dispatch(new XtmMetricsProcessMessage($limit));
			$this->bus->dispatch(new XtmLqaProcessMessage($limit));
			$this->bus->dispatch(new XtmStatisticsProcessMessage($limit));
			$this->bus->dispatch(new XtmProjectExtendedMessage($limit));
		} catch (\Throwable $e) {
			$this->loggerSrv->addInfo('Xtm process commands: execution failed: '.$e->getMessage());
		}

		$this->loggerSrv->addInfo('Xtm process commands: execution was successful');
	}
}
