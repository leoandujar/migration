<?php

namespace App\Connector\CustomerPortal\Response;

class TokenValidateResponse extends Response
{
	private bool $valid = false;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$value = array_shift($rawResponse);
			if (is_bool($value)) {
				$this->valid = $value;
			}
		}
	}

	public function isValid(): bool
	{
		return $this->valid;
	}
}
