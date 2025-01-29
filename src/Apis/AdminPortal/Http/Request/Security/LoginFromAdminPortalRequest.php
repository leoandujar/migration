<?php

namespace App\Apis\AdminPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiTokenConstraint;

class LoginFromAdminPortalRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiTokenConstraint]
	public mixed $token = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
