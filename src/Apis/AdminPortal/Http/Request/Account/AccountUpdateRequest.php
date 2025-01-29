<?php

namespace App\Apis\AdminPortal\Http\Request\Account;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class AccountUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $first_name = null;

	#[ApiStringConstraint]
	public mixed $last_name = null;

	#[ApiIntegerConstraint]
	public mixed $mobile = null;

	#[ApiStringConstraint]
	public mixed $department = null;

	#[ApiStringConstraint]
	public mixed $position = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
