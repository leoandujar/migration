<?php

namespace App\Workflow\Subscribers\XtmGithub;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Github\GithubService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Connector\Github\Response\PullResponse;
use App\Connector\Github\Response\LabelResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreatePullSubscriber implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private GithubService $githubService;
	private EntityManagerInterface $em;

	/**
	 * CreatePullSubscriber constructor.
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
			'workflow.github.completed.publish' => 'createPullRequest',
		];
	}

	public function createPullRequest(Event $event)
	{
		$history = $event->getSubject();
		$wf      = $this->registry->get($history, 'github');
		$context = $history->getContext();
		try {
			$this->loggerSrv->addInfo(sprintf('Creating a new pull request for project %s', $context['project_id']));
			$pullRequest = $this->githubService->createNewPullRequest($context['owner'], $context['repository'], $context['token'], $context['tree_title'], $context['pr_title']);
			if (null !== $pullRequest->id) {
				if ($pullRequest instanceof PullResponse) {
					$label = $this->githubService->createLabel($context['owner'], $context['repository'], $context['token'], $context['label_name'], $context['label_color']);
					if ($label instanceof LabelResponse) {
						$this->githubService->addLabel($context['owner'], $context['repository'], $context['token'], $label->name, $pullRequest->number);
					}
				}
				$context['pullRequest'] = $pullRequest;
			}
			if ($wf->can($history, 'new_pull')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'new_pull');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert(null, $thr);
			throw $thr;
		}
	}
}
