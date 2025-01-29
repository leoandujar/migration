<?php

namespace App\Connector\Xtrf\Request\Users;

use App\Connector\Xtrf\Request\Request;

class GetListRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/users';

	public function __construct()
	{
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
