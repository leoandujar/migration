<?php

namespace App\Connector\Xtm\Response;

class ProjectByIdResponse extends Response
{
	private array $projectData = [];

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			$this->projectData = $rawResponse;
		}
	}

	public function getProjectData(): array
	{
		return $this->projectData;
	}
}
