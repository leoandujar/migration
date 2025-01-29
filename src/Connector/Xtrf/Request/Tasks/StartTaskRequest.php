<?php

namespace App\Connector\Xtrf\Request\Tasks;

use App\Connector\Xtrf\Request\Request;

class StartTaskRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/tasks/%s/start';

	public function __construct(string $taksId)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->requestUri               = sprintf($this->requestUri, $taksId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
