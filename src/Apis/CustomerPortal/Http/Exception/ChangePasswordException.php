<?php

namespace App\Apis\CustomerPortal\Http\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ChangePasswordException extends AuthenticationException
{
	public array $additional = [];
}
