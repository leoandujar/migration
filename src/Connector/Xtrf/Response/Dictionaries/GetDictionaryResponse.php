<?php

namespace App\Connector\Xtrf\Response\Dictionaries;

use App\Connector\Xtrf\Response\Response;

class GetDictionaryResponse extends Response
{
	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}
}
