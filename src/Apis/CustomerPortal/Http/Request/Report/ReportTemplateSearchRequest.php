<?php

namespace App\Apis\CustomerPortal\Http\Request\Report;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ReportTemplateSearchRequest extends ApiRequest
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
	public mixed $name = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
