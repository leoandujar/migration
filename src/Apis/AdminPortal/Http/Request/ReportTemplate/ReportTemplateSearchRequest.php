<?php

namespace App\Apis\AdminPortal\Http\Request\ReportTemplate;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

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

	#[ApiIntegerConstraint]
	public mixed $format = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
