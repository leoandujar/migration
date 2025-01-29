<?php

namespace App\Flow\Services;

use App\Flow\Actions\Action;
use App\Flow\FlowService;
use App\Flow\FlowServiceFactory;

class ExecuteActionService
{
	private const GLOBAL_NAMESPACE = 'App\Flow\Actions\\';
	private FlowServiceFactory $flowSrvFactory;

	public function __construct(
		FlowServiceFactory $flowSrvFactory,
	) {
		$this->flowSrvFactory = $flowSrvFactory;
	}

	/**
	 * Contains the logic to execute an action centrally.
	 * Verify that it exists, contains its run() method and that everything has been executed correctly.
	 *
	 * @param string $action the name of the service to execute
	 * @param int    $id     the MonitorId
	 *
	 * @return int returns true if the service was executed correctly, otherwise it returns an error code
	 */
	public function excecuteAction(string $action, string $id, string $actionId, mixed &$specificInput = null, array $actionInputs, string $slug): int
	{
		$service = $this->flowSrvFactory->getAction(self::GLOBAL_NAMESPACE.$action);
		if (null !== $service) {
			if (method_exists($service, 'run')) {
				$service->setMonitorId($id);
				$service->setActionId($actionId);
				$service->setSpecificInput($specificInput);
				$service->setActionInputs($actionInputs);
				$service->setSlug($slug);
				$status = $service->run();
				if (Action::ACTION_STATUS_ERROR === $status) {
					return FlowService::ERROR_IN_SERVICE;
				}

				if (Action::PROCESS_STATUS_TERMINATE === $status) {
					return FlowService::TERMINATE_FLOW;
				}

				return FlowService::SERVICE_SUCCESS;
			} else {
				return FlowService::RUN_NOT_EXISTS;
			}
		} else {
			return FlowService::SERVICE_NOT_EXISTS;
		}
	}
}
