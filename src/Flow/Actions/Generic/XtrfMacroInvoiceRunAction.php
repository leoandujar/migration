<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\XtrfMacro\MacroConnector;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class XtrfMacroInvoiceRunAction extends Action
{
	public const ACTION_DESCRIPTION = 'Run macro to create invoice in XTRF';
	public const ACTION_INPUTS = [
		'projectsOrQuotes' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the projects or quotes to create invoice (with -projectId- key in array).',
		],
		'macro' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer',
			'description' => 'Macro ID to run.',
		],
	];

	public const ACTION_OUTPUTS = null;
	private MacroConnector $macroConn;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
		MacroConnector $macroConn,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->macroConn = $macroConn;
		$this->actionName = 'XtrfMacroInvoiceRunAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$xtrfRequests = $this->aux['projectsOrQuotes'];
		$macro = $this->aux['macro'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			foreach ($xtrfRequests as $xtrfRequest) {
				$projectId = $xtrfRequest['projectId'];
				$macroResponse = $this->macroConn->runMacro(
					$macro,
					[$projectId],
					$xtrfRequest['macroParams'],
					false,
				);
				if (!$macroResponse->isSuccessfull()) {
					$this->sendErrorMessage(
						'Error creating invoice macro response',
						[
							'id' => $projectId,
							'number' => $projectId,
							'message' => $macroResponse->getErrorMessage(),
						],
						null,
						"[FLOW]: Unable to create invoice in XTRF for monitor ID $this->monitorId and project $projectId. {$macroResponse->getErrorMessage()}"
					);

					continue;
				}
				if ($macroResponse->url) {
					$macroResult = file_get_contents($macroResponse->url);

					$jsonObject = json_decode($macroResult);
				}
				$this->sendSuccess(
					[
						'id' => $projectId,
						'number' => $projectId,
						'data' => $jsonObject,
					]
				);
			}

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
