<?php

namespace App\Apis\AdminPortal\Http\Request\CategoryGroup;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class CategoryGroupUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiBooleanConstraint]
	public mixed $active = null;

	#[ApiIntegerConstraint]
	public mixed $target = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
