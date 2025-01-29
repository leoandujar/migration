<?php

/*
 *  - COLLECTPROJECTS-ACTION -
 *
 *  This action searches for projects in the projects bucket based on various
 *  filters, recovering important information from their working directories.
 *
 *  -> Inputs:
 *     - filters: array with filters to search for projects.
 *     - level: string with the level of folders.
 *     - prefix: string with the prefix to remove from the path.
 *
 *  -> Outputs:
 *     - paths: array with the paths, project and language of the workingDirectory.
 *     - totalProjects: integer with the total of projects found (Notify uses that).
 *     - projectIds: array with the project ids (Notify uses that).
 *     - startDate: string with the start date of the projects (Notify uses that).
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Model\Entity\Project;
use App\Model\Entity\Task;
use App\Service\LoggerService;
use App\Service\UtilService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EntityProjectsCollectAction extends Action
{
	public const ACTION_DESCRIPTION = 'Get Projects from Database by filters';
	public const ACTION_INPUTS = [
		'filters' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Filters to search projects in the database.',
		],
		'level' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'Is a level for project or task',
		],
		'prefix' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'Prefix to name from the path.',
		],
	];

	public const ACTION_OUTPUTS = [
		'paths' => [
			'description' => 'List of paths, project and language of the workingDirectory.',
			'type' => 'array',
		],
		'totalProjects' => [
			'description' => 'Total of projects found.',
			'type' => 'integer',
		],
		'projectIds' => [
			'description' => 'List of project ids.',
			'type' => 'array',
		],
		'startDate' => [
			'description' => 'Start date of the projects.',
			'type' => 'string',
		],
	];

	private UtilService $utilsSrv;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		UtilService $utilsSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->utilsSrv = $utilsSrv;
		$this->actionName = 'EntityProjectsCollectAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filters = $this->aux['filters'];
		$level = $this->aux['level'];
		$prefix = $this->aux['prefix'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if (empty($filters)) {
				throw new BadRequestHttpException('[FLOW]: No filters was found. Unable to continue.');
			}

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
			$projectsList = $this->em->getRepository(Project::class)->getByFilters($queryFilters);
			$filesInfo = [];
			$projectIds = [];

			if ($projectsList) {
				/** @var Project $pro */
				foreach ($projectsList as $pro) {
					$projectIds[] = $pro->getIdNumber();
					if ('project' === $level) {
						/** @var Task $firstTask */
						$firstTask = $pro->getTasks()->first();
						if ($firstTask && !empty($firstTask->getWorkfileDirectory())) {
							$path = $firstTask->getWorkfileDirectory();
							if (!empty($prefix)) {
								$path = str_replace($prefix, '', $path);
							}
							$filesInfo[] = [
								'path' => $path,
								'language' => $firstTask->getTargetLanguage()?->getName(),
								'project' => $pro->getIdNumber(),
							];
							$this->loggerSrv->addInfo("[FLOW]: Adding project {$pro->getIdNumber()}.");
						}
					} else {
						$tasks = $pro->getTasks();
						/** @var Task $task */
						foreach ($tasks as $task) {
							if (!empty($task->getWorkfileDirectory())) {
								$path = $task->getWorkfileDirectory();
								if (!empty($prefix)) {
									$path = str_replace($prefix, '', $path);
								}
								$filesInfo[] = [
									'path' => $path,
									'language' => $task->getTargetLanguage()?->getName(),
									'project' => $pro->getIdNumber(),
								];
								$this->loggerSrv->addInfo("[FLOW]: Adding project {$pro->getIdNumber()}.");
							}
						}
					}
				}
			}

			if (!$filesInfo) {
				throw new BadRequestHttpException('[FLOW]: No workingDirectoy were found with value for workflow Attestation. Unable to continue');
			}

			$this->outputs = [
				'paths' => $filesInfo,
				'totalProjects' => count($projectsList),
				'projectIds' => $projectIds,
				'startDate' => isset($startDate) ? $startDate->format('m/d/Y') : null,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
