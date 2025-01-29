<?php

namespace App\Connector\Xtm\Response;

class MetricsByProjectIdResponse extends Response
{
	private array $data = [];

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			foreach ($rawResponse as $metric) {
				$this->data[$metric['targetLanguage']] = $metric;
			}
		}
	}

	public function getData(): array
	{
		return $this->data;
	}
}
