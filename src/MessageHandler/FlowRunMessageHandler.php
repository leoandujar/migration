<?php

namespace App\MessageHandler;

use App\Flow\Actions\Action;
use App\Flow\FlowService;
use App\Message\FlowRunMessage;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FlowRunMessageHandler
{
	private LoggerService $loggerSrv;
	private FlowService $flowProcess;

	public function __construct(
		LoggerService $loggerSrv,
		FlowService $flowProcess,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->flowProcess = $flowProcess;
	}

	public function __invoke(FlowRunMessage $message): void
	{
		$monitorId = $message->getMonitorId();
		$flowId = $message->getFlowId();

		$result = $this->flowProcess->runFlow($flowId, $monitorId);

		if (Action::ACTION_STATUS_ERROR === $result) {
			$this->loggerSrv->addError(
				'FlowRunMessageHandler. This flow got error while running',
				'Error running flow',
			);
		}
	}
}
