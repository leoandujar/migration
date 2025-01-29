<?php

namespace App\Apis\AdminPortal\Http\Request\Commands;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class RunCommandRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $params = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
