<?php

namespace App\Workflow\Subscribers\Attestation;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\Project;
use App\Model\Entity\Task;
use App\Model\Repository\ProjectRepository;
use App\Service\UtilService;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Workflow\HelperServices\MonitorLogService;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Model\Repository\WorkflowMonitorRepository;

class CollectInfo implements EventSubscriberInterface
{
	private Registry $registry;
	private UtilService $utilsSrv;
	private LoggerService $loggerSrv;
	private MonitorLogService $monitorLogSrv;
	private EntityManagerInterface $em;
	private ProjectRepository $projectRepo;
	private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		Registry $registry,
		UtilService $utilsSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		ProjectRepository $projectRepo,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->utilsSrv = $utilsSrv;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ATTESTATION);
		$this->projectRepo = $projectRepo;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.attestation.completed.start' => 'collectInfo',
		];
	}

	public function collectInfo(Event $event)
	{
		$this->loggerSrv->addInfo('Starting collect projects from filters');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		/** @var AVWorkflowMonitor $monitorObj */
		$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
		if ($monitorObj) {
			$this->monitorLogSrv->setMonitor($monitorObj);
		}
		$filters = $context['filters'];
		unset($context['filters']);

		if (empty($filters)) {
			$msg = 'No filters was found. Unable to continue.';
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		try {
			$queryFilters = [];
			if (!empty($filters['startDate'])) {
				/** @var \DateTime $date */
				$date = $this->utilsSrv->getDateByFormat($filters['startDate']);
				$startDate = clone $date->setTime(0, 0);
				$endDate = $date->setTime(23, 59, 59);
				$queryFilters['startDateStart'] = $startDate->format('Y-m-d H:i:s');
				$queryFilters['startDateEnd'] = $endDate->format('Y-m-d H:i:s');
			}
			$queryFilters['customerId'] = $filters['customerId'] ?? null;

			$projectsList = $this->projectRepo->getByFilters($queryFilters);
			$filesInfo = [];
			$projectIds = [];
			if ($projectsList) {
				/** @var Project $pro */
				foreach ($projectsList as $pro) {
					$projectIds[] = $pro->getIdNumber();
					/** @var Task $firstTask */
					$firstTask = $pro->getTasks()->first();
					if ($firstTask && !empty($firstTask->getWorkfileDirectory())) {
						$path = $firstTask->getWorkfileDirectory();
						if (!empty($context['params']['prefix'])) {
							$path = str_replace($context['params']['prefix'], '', $path);
						}
						$filesInfo[] = [
							'path' => $path,
							'language' => $firstTask->getTargetLanguage()?->getName(),
							'project' => $pro->getIdNumber(),
						];
						$this->loggerSrv->addInfo("Adding project {$pro->getIdNumber()} to collect info.");
					}
				}
			}

			if (!$filesInfo) {
				$msg = 'No workingDirectoy were found with value for workflow Attestation. Unable to continue';
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			$context['filesInfo'] = $filesInfo;
			$context['totalProjects'] = count($projectsList);
			$context['projectIds'] = $projectIds;
			$context['startDate'] = $startDate->format('m/d/Y') ?? null;
			$wf = $this->registry->get($history, 'attestation');

			if ($wf->can($history, 'collect')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'collect');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			$this->loggerSrv->addError('Error in CollectInfo step for Attestation workflow.', $thr);
			throw $thr;
		}
	}
}
