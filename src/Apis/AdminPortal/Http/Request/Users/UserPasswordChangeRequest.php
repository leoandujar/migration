<?php

namespace App\Apis\AdminPortal\Http\Request\Users;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiPasswordConstraint;

class UserPasswordChangeRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiPasswordConstraint]
	public mixed $password = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
