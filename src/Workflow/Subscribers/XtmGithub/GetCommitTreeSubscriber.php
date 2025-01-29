<?php

namespace App\Workflow\Subscribers\XtmGithub;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Github\GithubService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GetCommitTreeSubscriber implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private GithubService $githubService;
	private Registry $registry;
	private EntityManagerInterface $em;

	/**
	 * GetCommitTreeSubscriber constructor.
	 */
	public function __construct(
		LoggerService $loggerService,
		GithubService $githubService,
		Registry $registry,
		EntityManagerInterface $em
	) {
		$this->loggerSrv         = $loggerService;
		$this->githubService     = $githubService;
		$this->registry          = $registry;
		$this->em                = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.github.completed.prepare' => 'getCommitTree',
		];
	}

	/**
	 * @throws \Throwable
	 * @throws GuzzleException
	 */
	public function getCommitTree(Event $event)
	{
		$history = $event->getSubject();
		$wf      = $this->registry->get($history, 'github');
		$context = $history->getContext();
		$commit  = $context['latestCommit'];
		try {
			$this->loggerSrv->addInfo(sprintf('Fetching latest tree for project %s', $context['project_id']));
			$tree                = $this->githubService->getLatestTree($context['owner'], $context['repository'], $context['token'], $commit->commit->sha);
			$context['baseTree'] = $tree;
			if ($wf->can($history, 'tree')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'tree');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
