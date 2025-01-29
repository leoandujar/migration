<?php

namespace App\Connector\Xtm\Response;

class LoginResponse extends Response
{
	private string $token;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->token = $rawResponse['token'];
		}
	}

	public function getToken(): string
	{
		return $this->token;
	}
}
