<?php

namespace App\Connector\Xtrf\Request\Quote;

use App\Connector\Xtrf\Request\Request;

class GetQuoteRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/quotes/%s?embed=tasks';

	public function __construct(string $projectId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $projectId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
