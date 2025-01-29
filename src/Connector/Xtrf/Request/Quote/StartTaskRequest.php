<?php

namespace App\Connector\Xtrf\Request\Quote;

use App\Connector\Xtrf\Request\Request;

class StartTaskRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/quotes/%s/start';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $quoteID)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $quoteID);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
