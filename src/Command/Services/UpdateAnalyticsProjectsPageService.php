<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Linker\Managers\XtmProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAnalyticsProjectsPageService
{
	private XtmProjectManager $entityManager;
	private LoggerService $loggerSrv;

	public function __construct(
		LoggerService $loggerSrv,
		XtmProjectManager $entityManager
	) {
		$this->loggerSrv = $loggerSrv;
		$this->entityManager = $entityManager;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	/**
	 * @return int
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$list = $this->entityManager->updateProjectsPage($output, intval($input->getOption('page')), $input->getOption('finishedDate'));
		if (!$list) {
			$output->writeln('<entname>Chosen page does not exists.</entname>');

			return Command::FAILURE;
		}

		$output->writeln('Page <entval>'.$input->getOption('page').'</entval>/<entname>'.$this->entityManager->getPages().'</entname> processed.');
		$this->loggerSrv->addInfo('Analytics Projects: Page '.$input->getOption('page').' / '.$this->entityManager->getPages().' processed.');

		return Command::SUCCESS;
	}
}
