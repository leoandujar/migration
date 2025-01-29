<?php

namespace App\Connector\Xtrf\Request\Projects;

use App\Connector\Xtrf\Request\Request;

class CreateProjectRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/projects';
	protected string $type = Request::TYPE_JSON;

	public function __construct($params)
	{
		$this->params                  = $params;
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
