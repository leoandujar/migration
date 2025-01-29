<?php

namespace App\Apis\AdminPortal\Http\Request\ReportChart;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ReportChartSearchRequest extends ApiRequest
{

	#[ApiStringConstraint]
	public mixed $sort_order = null;

	#[ApiStringConstraint]
	public mixed $sort_by = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiStringConstraint]
	public mixed $code = null;

	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiBooleanConstraint]
	public mixed $active = null;

	#[ApiIntegerConstraint]
	public mixed $report_type_id = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
