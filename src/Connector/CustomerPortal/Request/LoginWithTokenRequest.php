<?php

namespace App\Connector\CustomerPortal\Request;

class LoginWithTokenRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/system/loginWithToken';

	public function __construct(string $token)
	{
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
		$this->params = [
			'accessToken' => $token,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
