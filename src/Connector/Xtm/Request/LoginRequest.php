<?php

namespace App\Connector\Xtm\Request;

class LoginRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/auth/token';

	public function __construct(string $clientId, string $userId, string $password)
	{
		$this->params = [
			'client'   => $clientId,
			'userId'   => $userId,
			'password' => $password,
		];
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
