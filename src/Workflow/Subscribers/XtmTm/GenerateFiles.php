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

class GenerateFiles implements EventSubscriberInterface
{
	private Registry $registry;
	private XtmConnector $xtmConnector;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	/**
	 * GenerateFiles constructor.
	 */
	public function __construct(
		Registry $registry,
		XtmConnector $xtmConnector,
		LoggerService $loggerSrv,
		EntityManagerInterface $em
	) {
		$this->registry     = $registry;
		$this->xtmConnector = $xtmConnector;
		$this->loggerSrv    = $loggerSrv;
		$this->em           = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_tm.completed.initialized' => 'generateFiles',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function generateFiles(Event $event)
	{
		/**
		 * @var $history WFHistory
		 */
		$history = $event->getSubject();
		$context = $history->getContext();
		$wf      = $this->registry->get($history, 'xtm_tm');
		try {
			$params = [
				'customerId' => $context['xtm_customer'],
				'fileType'   => $context['file_type'],
			];
			if ('false' == $context['batch']) {
				$params['changedDateFrom'] = $context['start'];
				$params['changedDateTo']   = $context['end'];
				$rsp                       = $this->xtmConnector->generateTranslationMemoryFile($params);
				if (null !== $rsp) {
					$context['files'] = [
						['id' => $rsp->getFileID()],
					];
				}
			} else {
				foreach ($context['external_ids'] as $id) {
					$params['projectId'] = $id;
					$this->loggerSrv->addInfo(sprintf('generating translation memory files for project %s', $id));
					$rsp = $this->xtmConnector->generateTranslationMemoryFile($params);
					if (null !== $rsp) {
						$context['files'][$id][] = ['id' => $rsp->getFileID()];
					}
				}
			}
			$history->setContext($context);
			if ($wf->can($history, 'generated')) {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'generated');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
