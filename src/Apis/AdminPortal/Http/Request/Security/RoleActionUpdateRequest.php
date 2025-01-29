<?php

namespace App\Apis\AdminPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiJsonConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class RoleActionUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiIntegerConstraint]
	public mixed $target = null;

	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiJsonConstraint]
	public mixed $abilities = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
