<?php

namespace App\Flow\Services;

use App\Flow\FlowService;

class GetErrorsService
{
	/**
	 * If there is a problem when trying to run an 'action', such that: the action does not exist,
	 * it does not have a run() method, or something went wrong during execution, this method will
	 * allow to obtain an error message to put it in the logger or something else.
	 *
	 * @param int $e It's an error code. Provides by executeAction method.
	 *
	 * @return string the message
	 */
	private function getErrorMessage(int $e): string
	{
		return match ($e) {
			FlowService::SERVICE_NOT_EXISTS => 'Service does not exist',
			FlowService::RUN_NOT_EXISTS => 'Method run does not exist in service',
			FlowService::ERROR_IN_SERVICE => 'Something went wrong in service',
			default => 'Unknown error',
		};
	}
}
