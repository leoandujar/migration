<?php

namespace App\Workflow\Subscribers\XtmTm;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Notify implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	/**
	 * DownloadFiles constructor.
	 */
	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em
	) {
		$this->registry  = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->em        = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_tm.completed.notify' => 'notify',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function notify(Event $event)
	{
		/**
		 * @var $history WFHistory
		 */
		$history = $event->getSubject();
		$context = $history->getContext();
		$wf      = $this->registry->get($history, 'xtm_tm');

		try {
			$history->setContext($context);
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
