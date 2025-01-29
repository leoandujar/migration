<?php

namespace App\Apis\CustomerPortal\Http\Request\Quote;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UpdateQuoteRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	public mixed $custom_fields = null;

	#[ApiStringConstraint]
	public mixed $instructions = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
