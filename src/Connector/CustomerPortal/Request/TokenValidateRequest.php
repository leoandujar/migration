<?php

namespace App\Connector\CustomerPortal\Request;

class TokenValidateRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/system/validateToken';

	public function __construct(string $token)
	{
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';

		$this->params = [
			'token' => $token,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
