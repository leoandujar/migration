<?php

namespace App\Apis\CustomerPortal\Http\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TwoFactorException extends AuthenticationException
{
	public array $additional = [];
}
