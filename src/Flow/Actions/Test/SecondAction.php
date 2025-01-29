<?php

namespace App\Flow\Actions\Test;

use App\Flow\Actions\Action;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class SecondAction extends Action
{
	public const ACTION_DESCRIPTION = 'Second action for testing purposes';
	public const ACTION_INPUTS = [
		'testArray' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'array',
		],
		'oneSpecificValue' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
		],
	];

	public const ACTION_OUTPUTS = null;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->actionName = 'SecondAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->setMonitorObject();
		$this->getInputs();
		$this->sendStartMessage();
		$testArray = $this->aux['testArray'] ?? null;

		foreach ($testArray as $key => $value) {
			$testArray[$key]['VALUE'] = $this->aux['oneSpecificValue'];
		}

		return self::ACTION_STATUS_OK;
	}
}
