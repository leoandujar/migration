<?php

namespace App\Connector\CustomerPortal\Request;

class ResetPasswordRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/system/account/password';

	public function __construct(string $token, string $newPassword)
	{
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';

		$this->params = [
			'token'                => $token,
			'password'             => $newPassword,
			'passwordConfirmation' => $newPassword,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
