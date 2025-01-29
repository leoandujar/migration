<?php

namespace App\Flow;

use App\Flow\Actions\Action;
use App\Flow\Services\ExecuteActionService;
use App\Model\Entity\AvFlow;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;

class FlowService
{
	public const SERVICE_SUCCESS = 0;
	public const SERVICE_NOT_EXISTS = -1;
	public const RUN_NOT_EXISTS = -2;
	public const ERROR_IN_SERVICE = -3;
	public const TERMINATE_FLOW = -4;
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private FlowServiceFactory $flowSrvFactory;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		WorkflowMonitorRepository $wfMonitorRepo,
		FlowServiceFactory $flowSrvFactory,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->flowSrvFactory = $flowSrvFactory;
	}

	public function runFlow(string $flowId, string $monitorId, ?array $parentActions = null, mixed &$forcedInput = null): string
	{
		$executeActionObj = new ExecuteActionService($this->flowSrvFactory);
		$actions = $parentActions ?? null;
		$currentAction = null;

		if (!$actions) {

			$flow = $this->em->getRepository(AvFlow::class)->findOneBy(['id' => $flowId]);
			if (!$flow) {
				$this->loggerSrv->addError('[FLOW]: Flow not found!.');

				return Action::ACTION_STATUS_ERROR;
			}

			$this->loggerSrv->addInfo('[FLOW]: Flow found!.');

			$actions = $flow->getActions();

			if (!$actions->count()) {
				$this->loggerSrv->addError('[FLOW]: Flow has no actions.');

				return Action::ACTION_STATUS_ERROR;
			}
			$currentAction = $flow->getStartAction();
			if (!$currentAction) {
				$this->loggerSrv->addError('[FLOW]: Flow has not starter action.');

				return Action::ACTION_STATUS_ERROR;
			}
		}

		do {
			$result = $executeActionObj->excecuteAction($currentAction->getAction(), $monitorId, $currentAction->getId(), $forcedInput, $currentAction->getInputs(), $currentAction->getSlug());
			if ($result < 0) {
				if (self::TERMINATE_FLOW === $result) {
					return Action::ACTION_STATUS_OK;
				}

				return Action::ACTION_STATUS_ERROR;
			}

			if (!$currentAction->getNext()) {
				break;
			}

			$currentAction = $currentAction->getNext();

		} while (1);

		return Action::ACTION_STATUS_OK;
	}
}
