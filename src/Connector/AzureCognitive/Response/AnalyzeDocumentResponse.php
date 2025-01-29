<?php

namespace App\Connector\AzureCognitive\Response;

class AnalyzeDocumentResponse extends Response
{
	private array $data = [];

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			$this->data = $rawResponse;
		}
	}

	public function getData(): array
	{
		return $this->data;
	}
}
