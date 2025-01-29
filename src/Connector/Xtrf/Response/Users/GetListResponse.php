<?php

namespace App\Connector\Xtrf\Response\Users;

use App\Connector\Xtrf\Response\Response;

class GetListResponse extends Response
{
	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->translateRaw();
		}
	}
}
