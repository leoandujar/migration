<?php

namespace App\Connector\Xtrf\Request\Customers;

use App\Connector\Xtrf\Request\Request;

class GetSinginTokenRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/customers/persons/accessToken';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $username)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->params                  = [
			'loginOrEmail' => $username,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
