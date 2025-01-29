<?php

namespace App\Flow\Actions\Test;

use App\Flow\Actions\Action;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class FirstAction extends Action
{
	public const ACTION_DESCRIPTION = 'First action for testing purposes';

	public const ACTION_INPUTS = [
		'testValueOne' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
		],
	];

	public const ACTION_OUTPUTS = [
		'testArray' => [
			'description' => 'Array with test values',
			'toAction' => true,
			'type' => 'array',
		],
	];

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->actionName = 'FirstAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		try {
			$this->setMonitorObject();
			$this->getInputs();
			$this->sendStartMessage();

			$testValueOne = $this->aux['testValueOne'] ?? null;

			$testArray = [];

			for ($i = 1; $i <= 15; ++$i) {
				$testArray[] = [
					'NAME' => 'NOMBRE CUALQUIERA '.$i,
					'VALUE' => $testValueOne.' '.$i,
				];
			}

			$this->outputs = [
				'testArray' => $testArray,
			];

			$this->setOutputs();

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Exception $e) {
			$this->loggerSrv->addError($e->getMessage());

			return self::ACTION_STATUS_ERROR;
		}
	}
}
