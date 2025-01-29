<?php

namespace App\Apis\AdminPortal\Http\Request\ContactPerson;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ContactPersonListRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiIntegerConstraint]
	public mixed $customer_id = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $type = null;

	#[ApiIntegerConstraint]
	public mixed $limit = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
