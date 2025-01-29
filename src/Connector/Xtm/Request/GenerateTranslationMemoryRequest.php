<?php

namespace App\Connector\Xtm\Request;

class GenerateTranslationMemoryRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/translation-memory/files/generate';

	public function __construct(string $authToken, array $params)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->params                   = $params;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
