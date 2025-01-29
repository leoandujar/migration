<?php

namespace App\Flow\Actions\Control;

use App\Flow\Actions\Action;
use App\Flow\FlowService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class ForeachAction extends Action
{
	private FlowService $flowSrv;

	public const ACTION_DESCRIPTION = 'Does an iterative subflow with an interable value';
	public const ACTION_INPUTS = [
		'foreach-with' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'The array to iterate',
			'canReplacedFor' => 'files',
		],
	];

	public const ACTION_OUTPUTS = null;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		FlowService $flowSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->inputs = ['foreach-with'];
		$this->actionName = 'ForeachAction';
		$this->flowSrv = $flowSrv;
	}

	public function run(): string
	{
		$actions = $this->getActions();
		if (!count($actions)) {
			$this->sendErrorMessage(
				'[FLOW]: A foreach child actions are null or not exists',
				['message' => 'No actions were found'],
				null,
				null
			);

			return self::ACTION_STATUS_ERROR;
		}

		$this->getInputs();

		$inputs = $this->aux['foreach-with'];
		$dataToWork = $this->getOneInput($inputs);

		foreach ($dataToWork as &$data) {
			$this->flowSrv->runFlow(0, $this->monitorId, $actions, $data);
		}

		$this->outputs = [
			$inputs => $dataToWork,
		];

		$this->setOutputs();

		$this->outputs = [];

		return self::ACTION_STATUS_OK;
	}
}
