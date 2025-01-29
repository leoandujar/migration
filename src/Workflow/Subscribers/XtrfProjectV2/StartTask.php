<?php

namespace App\Workflow\Subscribers\XtrfProjectV2;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Workflow\HelperServices\MonitorLogService;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Xtrf\XtrfConnector;
use App\Connector\Xtrf\Dto\ProjectDto;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Workflow\Services\XtrfProjectV2\Start;
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
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;

	/**
	 * StartTask constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		XtrfConnector $xtrf,
		Registry $registry,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationService,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->xtrf = $xtrf;
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->parameterBag = $parameterBag;
		$this->notificationService = $notificationService;
		$this->em = $em;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project_v2.completed.create' => 'startTask',
		];
	}

	/**
	 * @throws \Exception
	 */
	public function startTask(Event $event)
	{
		$this->loggerSrv->addInfo('Starting tasks for XtrfProject Workflow');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		$wf = $this->registry->get($history, 'xtrf_project_v2');

		try {
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
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

				if ($wf->can($history, 'start_tasks')) {
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
					$wf->apply($history, 'start_tasks');
					$this->loggerSrv->addInfo('All tasks started for XtrfProject Workflow');
					if ($wf->can($history, 'finish')) {
						$wf->apply($history, 'finish');
					}
				}
			}
		} catch (\Throwable $thr) {
			$this->monitorLogSrv->appendError([
				'message' => 'Workflow finished with error on Start tasks',
			]);
			$this->loggerSrv->addError('Workflow finished with error on Start tasks', $thr);
			$this->loggerSrv->alert($event, $thr);
			if ($wf->can($history, 'finish')) {
				$wf->apply($history, 'finish');
			}

			return;
		}
	}
}
