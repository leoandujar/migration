<?php

namespace App\Apis\CustomerPortal\Http\Request\Report;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class GenerateReportFromTemplateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $id = null;

	#[ApiIntegerConstraint]
	public mixed $format = null;

	#[ApiArrayConstraint]
	public mixed $report_types = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiArrayConstraint]
	public mixed $predefined_data = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
