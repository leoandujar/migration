<?php

namespace App\Flow\Actions\Test;

use App\Flow\Actions\Action;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class ThirdAction extends Action
{
	public const ACTION_DESCRIPTION = 'Third action for testing purposes';
	public const ACTION_INPUTS = null;
	public const ACTION_OUTPUTS = null;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->inputs = [];
	}

	public function run(): string
	{
		$this->getInputs();
		$testArray = $this->aux['testArray'] ?? null;

		$testArray[] = [
			'NAME' => 'OTRO NOMBRE CUALQUIERA',
		];

		return self::ACTION_STATUS_OK;
	}
}
