<?php

namespace App\Apis\AdminPortal\Http\Request\Flow;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class FlowUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $description = null;

	#[ApiBooleanConstraint]
	public mixed $run_automatically = null;

	#[ApiArrayConstraint]
	#[ApiNotBlankConstraint]
	public mixed $actions = null;

	#[ApiStringConstraint]
	#[ApiNotBlankConstraint]
	public mixed $startAction = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
