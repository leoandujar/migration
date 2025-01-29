<?php

namespace App\Connector\Xtm\Response;

class StatsByProjectIdResponse extends Response
{
	private array $data = [];
	private string $error = '';

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			foreach ($rawResponse as $metric) {
				if (!is_array($metric)) {
					continue;
				}
				$this->data[$metric['targetLanguage']] = $metric;
			}
		} else {
			$this->error = 'error';
		}
	}

	public function getData(): array
	{
		return $this->data;
	}

	public function successfull(): bool
	{
		if ('' == $this->error) {
			return true;
		}

		return false;
	}
}
