<?php

namespace App\Apis\AdminPortal\Http\Request\CategoryGroup;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

class CategoryGroupAssignRequest extends ApiRequest
{
	#[ApiIntegerConstraint]
	public mixed $id = null;

	#[ApiArrayConstraint]
	public mixed $groups = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
