<?php

namespace App\Workflow\Subscribers\XtmTm;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Service\Xtm\TranslationMemoryService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateZips implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private TranslationMemoryService $transMemSrv;
	private EntityManagerInterface $em;

	/**
	 * DownloadFiles constructor.
	 */
	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		TranslationMemoryService $transMemSrv,
		EntityManagerInterface $em
	) {
		$this->registry    = $registry;
		$this->loggerSrv   = $loggerSrv;
		$this->transMemSrv = $transMemSrv;
		$this->em          = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_tm.completed.extracted' => 'create',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function create(Event $event)
	{
		/**
		 * @var $history WFHistory
		 */
		$history = $event->getSubject();
		$context = $history->getContext();
		$wf      = $this->registry->get($history, 'xtm_tm');
		try {
			if ('true' == $context['batch']) {
				if ($handler = opendir($context['output'])) {
					while (false !== ($entry = readdir($handler))) {
						if (!in_array($entry, ['.', '..'])) {
							$filePath                  = sprintf('%s%s', $context['output'], $entry);
							$filePath                  = $this->transMemSrv->creteZip($filePath);
							$context['output_files'][] = $filePath;
						}
					}
					closedir($handler);
				}
			}
			$history->setContext($context);
			if ($wf->can($history, 'created')) {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'created');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
