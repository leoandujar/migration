<?php

namespace App\Apis\AdminPortal\Http\Request\CategoryGroup;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class CategoryGroupCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiNotBlankConstraint]
	#[ApiBooleanConstraint]
	public mixed $active = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $target = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
