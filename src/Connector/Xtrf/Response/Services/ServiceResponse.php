<?php

namespace App\Connector\Xtrf\Response\Services;

use App\Connector\Xtrf\Response\Response;

class ServiceResponse extends Response
{
	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->translateRaw();
		}
	}
}
