<?php

namespace App\Connector\Xtrf\Request\Users;

use App\Connector\Xtrf\Request\Request;

class GetSingleRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/users';

	public function __construct(int $userId)
	{
		$this->requestUri .= "/$userId";
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
