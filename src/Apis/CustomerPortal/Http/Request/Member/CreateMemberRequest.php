<?php

namespace App\Apis\CustomerPortal\Http\Request\Member;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayNotEmptyConstraint;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CreateMemberRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiEmailConstraint]
	public mixed $email = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $last_name = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayNotEmptyConstraint]
	public mixed $roles = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
