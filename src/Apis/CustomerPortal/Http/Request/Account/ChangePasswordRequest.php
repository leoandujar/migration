<?php

namespace App\Apis\CustomerPortal\Http\Request\Account;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiPasswordConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ChangePasswordRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $old_password;

	#[ApiNotBlankConstraint]
	#[ApiPasswordConstraint]
	public mixed $new_password;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
