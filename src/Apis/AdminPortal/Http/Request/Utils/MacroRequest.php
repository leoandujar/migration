<?php

namespace App\Apis\AdminPortal\Http\Request\Utils;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class MacroRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $macro_id = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $ids = null;

	#[ApiArrayConstraint]
	public mixed $params = null;

	#[ApiBooleanConstraint]
	public mixed $async = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
