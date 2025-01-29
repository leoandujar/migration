<?php

namespace App\Connector\Xtrf\Request\Tasks;

use App\Connector\Xtrf\Request\Request;

class ProgressTaskRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/tasks/%s/progress';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $taksId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $taksId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
