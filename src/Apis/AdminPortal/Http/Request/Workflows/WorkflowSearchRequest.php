<?php

namespace App\Apis\AdminPortal\Http\Request\Workflows;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class WorkflowSearchRequest extends ApiRequest
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

	#[ApiStringConstraint]
	public mixed $notification_type = null;

	#[ApiStringConstraint]
	public mixed $run_pattern = null;

	#[ApiBooleanConstraint]
	public mixed $run_automatically = null;

	#[ApiArrayConstraint]
	public mixed $category_groups = null;

	#[ApiArrayConstraint]
	public mixed $workflow_type = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
