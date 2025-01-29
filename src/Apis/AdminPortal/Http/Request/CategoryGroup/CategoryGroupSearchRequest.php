<?php

namespace App\Apis\AdminPortal\Http\Request\CategoryGroup;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class CategoryGroupSearchRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiBooleanConstraint]
	public mixed $active = null;

	#[ApiIntegerConstraint]
	public mixed $target = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
