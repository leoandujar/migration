<?php

namespace App\Workflow\Subscribers\XtmGithub;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Service\Notification\TeamNotification;
use App\Service\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MergePullSubscriber implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private NotificationService $notificationService;
	private EntityManagerInterface $em;

	/**
	 * MergePullSubscriber constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		NotificationService $notificationService,
		EntityManagerInterface $em
	) {
		$this->loggerSrv           = $loggerSrv;
		$this->registry            = $registry;
		$this->notificationService = $notificationService;
		$this->em                  = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.github.completed.new_pull' => 'mergePullRequest',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function mergePullRequest(Event $event)
	{
		$history = $event->getSubject();
		$wf      = $this->registry->get($history, 'github');
		$context = $history->getContext();
		try {
			foreach ($context['files'] as $key => $file) {
				$file->content          = '';
				$context['files'][$key] = $file;
			}
			if ($wf->can($history, 'finish')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'finish');
			}
			$this->loggerSrv->addInfo(sprintf('Sending notification for project %s', $context['project_id']));
			$this->sendNotification($context);
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}

	private function sendNotification($context)
	{
		if (0 === count($context['failed'])) {
			$data = [
				'message' => sprintf('The xtm project %s has been finished and delivered', $context['project_id']),
				'status'  => TeamNotification::STATUS_SUCCESS,
				'date'    => (new \DateTime())->format('Y-m-d'),
				'title'   => 'XTM Project finished and delivered',
			];
		} else {
			$data = [
				'message' => sprintf('Some files were not processed on project %s', $context['project_id']),
				'status'  => TeamNotification::STATUS_FAILURE,
				'date'    => (new \DateTime())->format('Y-m-d'),
				'title'   => 'XTM Project deliver failed',
				'failed'  => $context['failed'],
			];
		}
		$this->notificationService->addNotification(NotificationService::NOTIFICATION_TYPE_TEAM, $context['notification_target'], $data, 'XTM Project');
	}
}
