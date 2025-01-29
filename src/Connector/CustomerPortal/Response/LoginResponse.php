<?php

namespace App\Connector\CustomerPortal\Response;

class LoginResponse extends Response
{
	private mixed $jsessionid;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->jsessionid = $rawResponse['jsessionid'];
		}
	}

	public function getJsessionid(): string
	{
		return $this->jsessionid;
	}
}
