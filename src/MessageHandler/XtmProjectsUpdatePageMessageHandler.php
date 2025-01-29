<?php

namespace App\MessageHandler;

use App\Linker\Managers\XtmProjectManager;
use App\Message\XtmProjectsUpdatePageMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmProjectsUpdatePageMessageHandler
{
	private XtmProjectManager $entityManager;
	private LoggerService $loggerSrv;

	public function __construct(
		LoggerService $loggerSrv,
		XtmProjectManager $entityManager,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->entityManager = $entityManager;
	}

	public function __invoke(XtmProjectsUpdatePageMessage $message): void
	{
		$page = $message->getPage();
		$date = $message->getDate();
		$this->loggerSrv->addInfo('Checking and updating Analytics Projects per page (it may take a while)');
		$list = $this->entityManager->updateProjectsPage(intval($page, $date));
		if (!$list) {
			$this->loggerSrv->addError('Chosen page does not exists');
			return;
		}

		$this->loggerSrv->addInfo('Page <entval>'.$page.'</entval>/<entname>'.$this->entityManager->getPages().'</entname> processed.');
		$this->loggerSrv->addInfo('Analytics Projects: Page '.$page.' / '.$this->entityManager->getPages().' processed.');

	}
}
