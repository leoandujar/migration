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

class CreateNewTreeSubscriber implements EventSubscriberInterface
{
	private GithubService $githubService;
	private LoggerService $loggerSrv;
	private Registry $registry;
	private EntityManagerInterface $em;

	/**
	 * CreateNewTreeSubscriber constructor.
	 */
	public function __construct(
		GithubService $githubService,
		LoggerService $loggerSrv,
		Registry $registry,
		EntityManagerInterface $em
	) {
		$this->githubService     = $githubService;
		$this->loggerSrv         = $loggerSrv;
		$this->registry          = $registry;
		$this->em                = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.github.completed.tree' => 'createNewTree',
		];
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function createNewTree(Event $event)
	{
		$history  = $event->getSubject();
		$wf       = $this->registry->get($history, 'github');
		$context  = $history->getContext();
		$baseTree = $context['baseTree'];
		try {
			$this->loggerSrv->addInfo(sprintf('Creating a new tree for project %s', $context['project_id']));
			$newTree            = $this->githubService->createNewTree($context['owner'], $context['repository'], $context['token'], $baseTree->sha, $context['files']);
			$context['newTree'] = $newTree;
			if ($wf->can($history, 'new_tree')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'new_tree');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
