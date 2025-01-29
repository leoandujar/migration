<?php

namespace App\Connector\Boostlingo\Request;

class ClientRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/clients/client';

	public function __construct(string $queryString, string $token)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->headers['Authorization'] = "Bearer $token";
		parent::__construct($this->requestMethod, $this->requestUri.'?q='.$queryString, $this->params, $this->headers);
	}
}
