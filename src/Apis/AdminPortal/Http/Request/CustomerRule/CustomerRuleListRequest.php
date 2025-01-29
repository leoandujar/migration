<?php

namespace App\Apis\AdminPortal\Http\Request\CustomerRule;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CustomerRuleListRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name;

	#[ApiStringConstraint]
	public mixed $event;

	#[ApiStringConstraint]
	public mixed $type;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
