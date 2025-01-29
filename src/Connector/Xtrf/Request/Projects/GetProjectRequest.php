<?php

namespace App\Connector\Xtrf\Request\Projects;

use App\Connector\Xtrf\Request\Request;

class GetProjectRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/projects/%s?embed=tasks';

	public function __construct(string $projectId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $projectId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
