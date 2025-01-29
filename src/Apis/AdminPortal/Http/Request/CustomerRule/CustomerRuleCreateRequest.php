<?php

namespace App\Apis\AdminPortal\Http\Request\CustomerRule;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CustomerRuleCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIdentifierConstraint]
	#[ApiIntegerConstraint]
	public mixed $customer_id = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $event = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $type = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiIntegerConstraint]
	public mixed $workflow_id = null;

	#[ApiArrayConstraint]
	public mixed $parameters = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
