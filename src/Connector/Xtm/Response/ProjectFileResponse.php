<?php

namespace App\Connector\Xtm\Response;

class ProjectFileResponse extends Response
{
	private array $data = [];

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			if (!empty($rawResponse)) {
				$this->data = array_shift($rawResponse);
			}
		}
	}

	public function getData(): array
	{
		return $this->data;
	}
}
