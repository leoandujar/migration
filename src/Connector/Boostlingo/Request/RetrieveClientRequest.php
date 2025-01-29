<?php

namespace App\Connector\Boostlingo\Request;

class RetrieveClientRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/company-account/company/%s';

	public function __construct(string $clientId, string $token)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->headers['Authorization'] = "Bearer $token";
		$this->requestUri = sprintf($this->requestUri, $clientId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
