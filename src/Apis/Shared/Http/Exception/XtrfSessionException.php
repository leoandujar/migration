<?php

namespace App\Apis\Shared\Http\Exception;

class XtrfSessionException extends \Exception implements \Throwable
{
	public function __construct($message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
