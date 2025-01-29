<?php

namespace App\Connector\Xtrf\Response\Tasks;

use App\Connector\Xtrf\Response\Response;

class TaskResponse extends Response
{
	private array $taskData;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->taskData = $rawResponse;
		}
	}

	/**
	 * @return mixed
	 */
	public function getTaskData(): array
	{
		return $this->taskData;
	}
}
