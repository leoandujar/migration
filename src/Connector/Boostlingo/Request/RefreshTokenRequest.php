<?php

namespace App\Connector\Boostlingo\Request;

class RefreshTokenRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/account/refresh-access-token';

	public function __construct(string $refreshToken)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->params = [
			'refreshToken' => $refreshToken,
		];

		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
