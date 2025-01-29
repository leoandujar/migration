<?php

namespace App\Apis\CustomerPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class PublicLoginRequest extends ApiRequest
{
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
