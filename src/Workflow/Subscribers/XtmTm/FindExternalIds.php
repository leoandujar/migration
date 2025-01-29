<?php

namespace App\Workflow\Subscribers\XtmTm;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\ProjectRepository;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FindExternalIds implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private ProjectRepository $projectRepository;
	private EntityManagerInterface $em;

	/**
	 * FindProjects constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		ProjectRepository $projectRepository,
		EntityManagerInterface $em
	) {
		$this->loggerSrv         = $loggerSrv;
		$this->registry          = $registry;
		$this->projectRepository = $projectRepository;
		$this->em                = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_tm.completed.start' => 'getProjects',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function getProjects(Event $event)
	{
		try {
			/**
			 * @var WFHistory $history
			 */
			$history = $event->getSubject();
			$context = $history->getContext();
			$wf      = $this->registry->get($history, 'xtm_tm');
			if ('true' == $context['batch']) {
				$projects    = $this->projectRepository->getProjects($context['customer'], $context['range_field'], $context['start'], $context['end']);
				$externalIDs = [];
				foreach ($projects as $project) {
					foreach ($project->getAnalyticsProjects() as $ap) {
						if (!array_key_exists($ap->getExternalId(), $externalIDs)) {
							$externalIDs[$ap->getExternalId()] = $ap->getExternalId();
						}
					}
				}
				$context['external_ids'] = array_keys($externalIDs);
			}
			$history->setContext($context);
			if ($wf->can($history, 'initialized')) {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'initialized');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
