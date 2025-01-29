<?php

namespace App\Apis\AdminPortal\Http\Request\ReportType;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiTextConstraint;

class ReportTypeUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiStringConstraint]
	public mixed $function_name = null;

	#[ApiTextConstraint]
	public mixed $description = null;

	#[ApiStringConstraint]
	public mixed $parent = null;

	#[ApiArrayConstraint]
	public mixed $children = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
