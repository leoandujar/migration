<?php

namespace App\Apis\CustomerPortal\Http\Request\Report;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class GenerateReportRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $id = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $format = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $report_types = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiNotBlankConstraint]
	public mixed $template = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
