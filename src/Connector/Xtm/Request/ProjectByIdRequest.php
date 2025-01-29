<?php

namespace App\Connector\Xtm\Request;

class ProjectByIdRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/projects/%s';

	public function __construct(string $projectId, $authToken)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->requestUri               = sprintf($this->requestUri, $projectId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
