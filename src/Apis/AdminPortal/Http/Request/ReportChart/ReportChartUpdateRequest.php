<?php

namespace App\Apis\AdminPortal\Http\Request\ReportChart;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ReportChartUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $description = null;
	
	#[ApiStringConstraint]
	public mixed $slug = null;
	
	#[ApiStringConstraint]
	public mixed $category = null;
	
	#[ApiBooleanConstraint]
	public mixed $active = null;
	
	#[ApiIntegerConstraint]
	public mixed $report_type_id = null;
	
	#[ApiIntegerConstraint]
	public mixed $size = null;
	
	#[ApiArrayConstraint]
	public mixed $options = null;
	
	#[ApiStringConstraint]
	public mixed $return_y = null;
	
	#[ApiIntegerConstraint]
	public mixed $type = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
