<?php

namespace App\Apis\AdminPortal\Http\Request\Customer;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class CustomerListRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiIntegerConstraint]
	public mixed $limit = null;

	#[ApiBooleanConstraint]
	public mixed $bl = null;

	#[ApiBooleanConstraint]
	public mixed $onboarded = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = true;
		$this->enablePagination = false;
		parent::__construct($values);
	}
}
