<?php

namespace App\Apis\AdminPortal\Http\Request\Users;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNameConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiPasswordConstraint;
use App\Apis\Shared\Http\Validator\ApiUserNameConstraint;

class UserCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiUserNameConstraint]
	public mixed $username = null;

	#[ApiNotBlankConstraint]
	#[ApiNameConstraint]
	public mixed $first_name = null;

	#[ApiNotBlankConstraint]
	#[ApiNameConstraint]
	public mixed $last_name = null;

	#[ApiNotBlankConstraint]
	#[ApiEmailConstraint]
	public mixed $email = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $roles = null;

	#[ApiArrayConstraint]
	public mixed $category_groups = null;

	#[ApiPasswordConstraint]
	public mixed $password = null;

	#[ApiIntegerConstraint]
	public mixed $type = null;

	#[ApiIntegerConstraint]
	public mixed $status = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
