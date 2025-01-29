<?php

namespace App\Workflow\Subscribers\EmailParsing;

use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Workflow\Services\XtrfProject\Start;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectCreate implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private Start $wfXtrfProject;
	private EntityManagerInterface $em;

	/**
	 * Prepare constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Start $wfXtrfProject,
		Registry $registry,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->wfXtrfProject = $wfXtrfProject;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_EMAIL_PARSING);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.email_parsing.completed.prepare_data' => 'projectCreate',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function projectCreate(Event $event)
	{
		try {
			/** @var WFHistory $history */
			$history = $event->getSubject();
			$context = $history->getContext();
			$wf = $this->registry->get($history, 'email_parsing');
			$wfParams = new WFParams();
			$wfParams
				->setNotificationType($context['notification_type'])
				->setNotificationTarget($context['notification_target'])
				->setParams($context['params']);
			$this->loggerSrv->addInfo("Creating project: {$wf->getName()} from EmailParsing fields.");
			$this->wfXtrfProject->Run(uniqid('project_'), $wfParams);

			if ($wf->can($history, 'project_create')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'project_create');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating project from EmailParsing workflow.', $thr);
			throw $thr;
		}
	}
}
