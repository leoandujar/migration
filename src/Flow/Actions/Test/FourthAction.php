<?php

namespace App\Flow\Actions\Test;

use App\Flow\Actions\Action;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class FourthAction extends Action
{
	public const ACTION_DESCRIPTION = 'Fourth action for testing purposes';
	public const ACTION_INPUTS = null;
	public const ACTION_OUTPUTS = null;

	public function __construct(
		EntityManagerInterface $em,
		WorkflowMonitorRepository $wfMonitorRepo,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->inputs = [];
	}

	public function run(): string
	{
		$this->getInputs();

		$this->specificInput['third'] = "I'm the third value added";

		return self::ACTION_STATUS_OK;
	}
}
