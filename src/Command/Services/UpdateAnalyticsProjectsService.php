<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Connector\Xtm\XtmConnector;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Connector\Xtm\Response\ProjectsCountResponse;

class UpdateAnalyticsProjectsService
{
	private LoggerService $loggerSrv;
	private XtmConnector $xtmConnector;

	public function __construct(
		LoggerService $loggerSrv,
		XtmConnector $xtmConnector
	) {
		$this->loggerSrv = $loggerSrv;
		$this->xtmConnector = $xtmConnector;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function execute(InputInterface $input, OutputInterface $output): void
	{
		$finishedDateFrom = null;
		if (!empty($input->getOption('finishedDate'))) {
			$finishedDateFrom = $input->getOption('finishedDate');
		}

		/** @var ProjectsCountResponse $response */
		$response = $this->xtmConnector->projectsCount($finishedDateFrom);
		$pages = 0;
		if (null !== $response) {
			$pages = $response->getTotalPages();
		}

		$output->writeln('pages to process: '.$pages);
		$this->loggerSrv->addInfo('Analytics Projects: pages to process: '.$pages);

		for ($a = intval($input->getOption('start')); $a <= $pages; ++$a) {
			$process = Process::fromShellCommandline('php -dxdebug.remote_autostart=On ./bin/console worker:update:analytics-projects:page --ansi --finishedDate  '.$finishedDateFrom.' --page '.$a.' 2>&1');
			$process->setTimeout(3600);
			$process->setIdleTimeout(3600);
			$process->start();
			foreach ($process as $type => $data) {
				$output->write($data);
				if ($process::ERR === $type) {
					break;
				}
			}
		}
	}
}
