<?php

namespace App\Workflow\Subscribers\XtmGithub;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Github\GithubService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PushCommitSubscriber implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private $githubService;
	private EntityManagerInterface $em;

	/**
	 * PushCommitSubscriber constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		GithubService $githubService,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->githubService = $githubService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.github.completed.new_commit' => 'pushCommit',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function pushCommit(Event $event)
	{
		$history = $event->getSubject();
		$wf = $this->registry->get($history, 'github');
		$context = $history->getContext();
		$newCommit = $context['newCommit'];
		try {
			$this->loggerSrv->addInfo(sprintf('Creating a reference %s for project %s', $context['project_id'], $context['pr_title']));
			$pushCommit = $this->githubService->pushCommitForReference($context['owner'], $context['repository'], $context['token'], $context['tree_title'], $newCommit->sha);
			$context['pushedCommit'] = $pushCommit;
			if ($wf->can($history, 'publish')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'publish');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
