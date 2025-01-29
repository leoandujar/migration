<?php

namespace App\Apis\AdminPortal\Http\Request\Opi;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

class InvoiceCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $customer = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $between = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $service = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $specialization = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $macro = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $source_language = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
