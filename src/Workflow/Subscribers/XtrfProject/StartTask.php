<?php

namespace App\Workflow\Subscribers\XtrfProject;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Xtrf\XtrfConnector;
use App\Connector\Xtrf\Dto\ProjectDto;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Workflow\Services\XtrfProject\Start;
use App\Connector\CustomerPortal\Dto\QuoteDto;
use App\Service\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StartTask implements EventSubscriberInterface
{
	private XtrfConnector $xtrf;
	private LoggerService $loggerSrv;
	private Registry $registry;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationService;
	private EntityManagerInterface $em;

	/**
	 * StartTask constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		XtrfConnector $xtrf,
		Registry $registry,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationService,
		EntityManagerInterface $em
	) {
		$this->xtrf = $xtrf;
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->parameterBag = $parameterBag;
		$this->notificationService = $notificationService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project.completed.configured' => 'start',
		];
	}

	/**
	 * @throws \Exception
	 */
	public function start(Event $event)
	{
		try {
			/**
			 * @var WFHistory $history
			 */
			$history = $event->getSubject();
			$wf = $this->registry->get($history, 'xtrf_project');
			$context = $history->getContext();
			if (!empty($context['request']['data'])) {
				foreach ($context['request']['data'] as $key => $request) {
					if ($request['info'] instanceof ProjectDto || $request['info'] instanceof QuoteDto) {
						$id = $request['info']->id;
					} else {
						$id = $request['info']['id'];
					}

					if (true === boolval($context['start_tasks'])) {
						switch ($context['type']) {
							case Start::TYPE_PROJECT:
								$project = $this->xtrf->getProject($id)->getProject();
								$tasks = $project->tasks;
								foreach ($tasks as $index => $task) {
									$request['info']->tasks[$index]['id'] = $task;
									$response = $this->xtrf->startTask($task['id']);
									if (null !== $response && $response->isSuccessfull()) {
										$response = $this->xtrf->getTaskProgress($task['id']);
										if ($response->isSuccessfull() && null !== $response->getTaskData()) {
											$request['info']->task[$index]['status'] = $response->getTaskData()['status'];
										}
									}
								}
								break;
							case Start::TYPE_QUOTE:
								$response = $this->xtrf->quoteStartTasks($request['info']->id);
								if (!$response->isSuccessfull()) {
									$this->loggerSrv->addError(sprintf('Unable to start quote %s tasks', $request['info']->id));
								}
								break;
						}
					}
					$data = [];
					switch ($context['notification_type']) {
						case NotificationService::NOTIFICATION_TYPE_TEAM:
							if (count($context['files'])) {
								$data = [
									'message' => $context['info'],
									'status' => $context['status'],
									'date' => (new \DateTime())->format('Y-m-d'),
									'link' => count($context['request']['links']) ? $context['request']['links'][$key] : 'No defined',
									'title' => $history->getName(),
								];
							}
							break;
						case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
							$template = $this->parameterBag->get('app.postmark.tpl_id.workflow');
							if (!empty($template)) {
								$data = [
									'template' => $template,
									'workflow' => $history->getName(),
									'link' => count($context['request']['links']) ? $context['request']['links'][$key] : 'No File generated',
								];
							}
							break;
					}
					$this->notificationService->addNotification($context['notification_type'], $context['notification_target'], $data, $history->getName());
					$context['request']['data'][$key] = $request;
				}

				if ($wf->can($history, 'finished')) {
					foreach ($context['files'] as $key => &$file) {
						$file['content'] = '';
						$context['files'][$key] = $file;
					}
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
					$wf->apply($history, 'finished');
				}
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
