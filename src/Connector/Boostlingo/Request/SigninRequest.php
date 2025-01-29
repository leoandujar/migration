<?php

namespace App\Connector\Boostlingo\Request;

class SigninRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/account/signin';

	public function __construct(string $email, string $password)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->params = [
			'email' => $email,
			'password' => $password,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
