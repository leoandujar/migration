<?php

namespace App\Workflow\Subscribers\XtmTm;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Xtm\XtmConnector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DownloadFiles implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private XtmConnector $xtmConnector;
	private EntityManagerInterface $em;

	/**
	 * DownloadFiles constructor.
	 */
	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		XtmConnector $xtmConnector,
		EntityManagerInterface $em
	) {
		$this->registry     = $registry;
		$this->loggerSrv    = $loggerSrv;
		$this->xtmConnector = $xtmConnector;
		$this->em           = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_tm.completed.generated' => 'download',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function download(Event $event)
	{
		/**
		 * @var $history WFHistory
		 */
		$history = $event->getSubject();
		$context = $history->getContext();
		$wf      = $this->registry->get($history, 'xtm_tm');
		try {
			$waitingList = [];
			foreach ($context['files'] as $externalID => $files) {
				foreach ($files as $key => $file) {
					$fileStatus = $this->xtmConnector->translationMemoryFileStatus($file['id'] ?? $file);
					if (!$fileStatus->isReady()) {
						$waitingList[] = $file['id'] ?? $file;
						continue;
					}
					$rsp = $this->xtmConnector->downloadTranslationMemoryFiles($file['id'] ?? $file);
					if (is_array($context['files'][$externalID][$key])) {
						$context['files'][$externalID][$key]['path'] = $rsp->getFilePath();
					} else {
						$context['files'][$externalID]['path'] = $rsp->getFilePath();
					}
				}
			}
			foreach ($waitingList as $file) {
				$counter    = 0;
				$fileStatus = $this->xtmConnector->translationMemoryFileStatus($file);
				while (!$fileStatus->isReady() && $counter < 3) {
					++$counter;
					usleep($context['waiting_time']*1000000 ?? 15000000);
					$fileStatus = $this->xtmConnector->translationMemoryFileStatus($file);
				}
				if (3 === $counter && !$fileStatus->isReady()) {
					throw new \Exception(sprintf('the file %s was not ready after 30 seconds', $file));
				}
			}
			$history->setContext($context);
			if ($wf->can($history, 'downloaded')) {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'downloaded');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Workflow finished with error', $thr);
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
