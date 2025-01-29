<?php

/*
 *  - STARTTASKXTRF-ACTION -
 *  Based on previously created projects or quotes (that's right, the
 *  action: "CreateXtrfprojectQuoteAction" should be executed before this),
 *  it will start the tasks.
 *
 *  -> Inputs:
 *    - projectsOrQuotes: array with projects or quotes.
 *    - type: string with the type of project or quote.
 *
 *  -> Outputs:
 *    - projectsOrQuotes: array with status of tasks.
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\Xtrf\XtrfConnector;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class XtrfTaskStartAction extends Action
{
	public const ACTION_DESCRIPTION = 'Start a task on XTRF';
	public const ACTION_INPUTS = [
		'projectsOrQuotes' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the projects or quotes to start task.',
		],
		'type' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				'project',
				'quote',
			],
			'description' => 'String with the type of project or quote.',
		],
	];

	public const ACTION_OUTPUTS = [
		'projectsOrQuotes' => [
			'description' => 'Overwrite Array with status of tasks.',
			'type' => 'array',
		],
	];
	private const TYPE_PROJECT = 'project';
	private const TYPE_QUOTE = 'quote';
	private XtrfConnector $xtrf;

	public function __construct(
		LoggerService $loggerSrv,
		XtrfConnector $xtrfConnector,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->xtrf = $xtrfConnector;
		$this->actionName = 'XtrfTaskStartAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$projectsOrQuotes = $this->aux['projectsOrQuotes'];
		$type = $this->aux['type'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			foreach ($projectsOrQuotes as $poq) {
				$id = $poq->id;
				switch ($type) {
					case self::TYPE_PROJECT:
						$project = $this->xtrf->getProject($id)->getProject();
						$tasks = $project->tasks;
						foreach ($tasks as $index => $task) {
							$poq->tasks[$index]['id'] = $task;
							$response = $this->xtrf->startTask($task['id']);
							if (null !== $response && $response->isSuccessfull()) {
								$this->loggerSrv->addInfo('[FLOW]: A task has been started.');
								$response = $this->xtrf->getTaskProgress($task['id']);
								if ($response->isSuccessfull() && null !== $response->getTaskData()) {
									$poq->task[$index]['status'] = $response->getTaskData()['status'];
									$this->loggerSrv->addInfo("[FLOW]: Task project {$poq->id} status: {$response->getTaskData()['status']}.");
								}
							}
						}
						break;
					case self::TYPE_QUOTE:
						$response = $this->xtrf->quoteStartTasks($poq->id);
						if (!$response->isSuccessfull()) {
							$this->sendErrorMessage(
								'[FLOW]: Unable to start quote %s tasks'.$poq->id,
								[
									'message' => $response->getErrorMessage(),
								],
								null,
								null
							);
						}
						break;
				}
			}

			$this->outputs = [
				'projectsOrQuotes' => $projectsOrQuotes,
			];

			$this->setOutputs();

			$this->sendSuccessMessage();

			$this->outputs = [];

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
