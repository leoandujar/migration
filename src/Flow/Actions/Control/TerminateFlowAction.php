<?php

namespace App\Flow\Actions\Control;

use App\Flow\Actions\Action;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class TerminateFlowAction extends Action
{
	private const TERMINATE_STATUS_CRITICAL = 'critical';
	private const TERMINATE_STATUS_NORMAL = 'normal';
	private const TERMINATE_STATUS_ERROR = 'error';

	public const ACTION_DESCRIPTION = 'Terminate the flow with a message and status.';

	public const ACTION_INPUTS =  [
		'status' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				self::TERMINATE_STATUS_CRITICAL,
				self::TERMINATE_STATUS_NORMAL,
				self::TERMINATE_STATUS_ERROR,
			],
			'description' => 'The status of the termination',
		],
		'message' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'The message to log',
		],
	];

	public const ACTION_OUTPUTS = null;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->actionName = 'TerminateFlowAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$code = $this->aux['status'];
		$msg = $this->aux['message'] ?? 'No message provided';

		switch ($code) {
			case self::TERMINATE_STATUS_CRITICAL:
				$this->loggerSrv->addCritical('Flow terminated due to critical error', [
					'terminate-msg' => $msg,
				]);
				break;
			case self::TERMINATE_STATUS_NORMAL:
				$this->loggerSrv->addInfo('Flow terminated as a normal flow', [
					'terminate-msg' => $msg,
				]);
				break;
			case self::TERMINATE_STATUS_ERROR:
				$this->loggerSrv->addError('Flow terminated due to error', [
					'terminate-msg' => $msg,
				]);
				break;
			default:
				$this->loggerSrv->addWarning('Flow terminated with unknown status', [
					'terminate-msg' => $msg,
				]);
				break;
		}

		return self::PROCESS_STATUS_TERMINATE;
	}
}
