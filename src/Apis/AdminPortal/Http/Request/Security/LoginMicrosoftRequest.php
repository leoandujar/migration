<?php

namespace App\Apis\AdminPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class LoginMicrosoftRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	public mixed $code = null;

	#[ApiNotBlankConstraint]
	public mixed $state = null;

	#[ApiNotBlankConstraint]
	public mixed $session_state = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
