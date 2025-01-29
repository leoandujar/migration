<?php

namespace App\Connector\CustomerPortal\Request;

class LoginRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/system/login';

	public function __construct(string $username, string $password)
	{
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
		$this->params                  = [
			'username' => $username,
			'password' => $password,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
