<?php

namespace App\Connector\Xtrf\Request\Services;

use App\Connector\Xtrf\Request\Request;

class GetAllRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/services/all';

	public function __construct()
	{
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
