<?php

namespace App\Apis\AdminPortal\Http\Request\ReportTemplate;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiHtmlConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

class ReportTemplateUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiArrayConstraint]
	public mixed $charts = null;

	#[ApiArrayConstraint]
	public mixed $category_groups = null;

	#[ApiIntegerConstraint]
	public mixed $format = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiArrayConstraint]
	public mixed $predefined_data = null;

	#[ApiHtmlConstraint]
	public mixed $template = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
