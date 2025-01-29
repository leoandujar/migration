<?php

namespace App\Connector\CustomerPortal\Request;

class ChangePasswordRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri    = '/system/account/password';

	public function __construct(string $sessionId, string $oldPassword, string $newPassword)
	{
		$this->headers['Cookie']       = sprintf('JSESSIONID=%s', $sessionId);
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
		$this->params                  = [
			'oldPassword'             => $oldPassword,
			'newPassword'             => $newPassword,
			'newPasswordConfirmation' => $newPassword,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
