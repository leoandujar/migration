<?php

namespace App\Apis\CustomerPortal\Http\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class EmailException extends AuthenticationException
{
	public array $additional = [];
}
