<?php

namespace App\Apis\AdminPortal\Http\Request\CustomerRule;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CustomerRuleUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $event = null;

	#[ApiStringConstraint]
	public mixed $type = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiIntegerConstraint]
	public mixed $workflow_id = null;

	#[ApiArrayConstraint]
	public mixed $parameters;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
