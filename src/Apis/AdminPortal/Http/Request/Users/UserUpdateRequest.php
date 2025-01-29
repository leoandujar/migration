<?php

namespace App\Apis\AdminPortal\Http\Request\Users;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNameConstraint;
use App\Apis\Shared\Http\Validator\ApiUserNameConstraint;

class UserUpdateRequest extends ApiRequest
{
	#[ApiNameConstraint]
	public mixed $first_name = null;

	#[ApiNameConstraint]
	public mixed $last_name = null;

	#[ApiEmailConstraint]
	public mixed $email = null;

	#[ApiArrayConstraint]
	public mixed $roles = null;

	#[ApiArrayConstraint]
	public mixed $category_groups = null;

	#[ApiIntegerConstraint]
	public mixed $status = null;

	#[ApiUserNameConstraint]
	public mixed $username = null;

	#[ApiBooleanConstraint]
	public mixed $all_customers_access = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
