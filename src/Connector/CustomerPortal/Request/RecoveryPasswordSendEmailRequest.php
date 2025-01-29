<?php

namespace App\Connector\CustomerPortal\Request;

class RecoveryPasswordSendEmailRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/system/mail/resetPassword';

	public function __construct(string $email)
	{
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';

		$this->params = [
			'loginOrEmail' => $email,
			'key'          => '',
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
