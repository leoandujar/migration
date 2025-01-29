<?php

namespace App\Apis\CustomerPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class PublicLoginCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $first_name = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $last_name = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $customer_id = null;

	#[ApiNotBlankConstraint]
	#[ApiEmailConstraint]
	public mixed $email = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
