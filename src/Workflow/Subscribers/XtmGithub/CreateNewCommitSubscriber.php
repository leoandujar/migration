<?php

namespace App\Workflow\Subscribers\XtmGithub;

use Doctrine\ORM\EntityManager;
use Throwable;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Github\GithubService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateNewCommitSubscriber implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private GithubService $githubService;
	private EntityManagerInterface $em;

	/**
	 * CreateNewCommitSubscriber constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		GithubService $githubService,
		EntityManagerInterface $em
	) {
		$this->loggerSrv         = $loggerSrv;
		$this->registry          = $registry;
		$this->githubService     = $githubService;
		$this->em                = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.github.completed.new_tree' => 'createNewCommit',
		];
	}

	/**
	 * @throw Throwable
	 */
	public function createNewCommit(Event $event)
	{
		$history    = $event->getSubject();
		$wf         = $this->registry->get($history, 'github');
		$context    = $history->getContext();
		$lastCommit = $context['latestCommit'];
		$tree       = $context['newTree'];
		try {
			$this->loggerSrv->addInfo(sprintf('Creating a new commit for project %s', $context['project_id']));
			$newCommit = $this->githubService->createNewCommit($context['owner'], $context['repository'], $context['token'], $lastCommit->commit->sha, $tree->sha, sprintf(
				'Update translation - %s',
				$context['project_id']
			));
			$context['newCommit'] = $newCommit;
			if ($wf->can($history, 'new_commit')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'new_commit');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
