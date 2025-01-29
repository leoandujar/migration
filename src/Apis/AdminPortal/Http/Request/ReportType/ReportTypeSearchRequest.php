<?php

namespace App\Apis\AdminPortal\Http\Request\ReportType;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ReportTypeSearchRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $sort_order = null;

	#[ApiStringConstraint]
	public mixed $sort_by = null;

	#[ApiIntegerConstraint]
	public mixed $limit = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiArrayConstraint]
	public mixed $chart_type = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
