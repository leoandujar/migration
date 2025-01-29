<?php

namespace App\Apis\AdminPortal\Http\Request\Workflows;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class WorkflowMonitorListRequest extends ApiRequest
{
	#[ApiIdentifierConstraint]
	public mixed $internal_user_id = null;

	#[ApiArrayConstraint]
	public mixed $status = null;

	#[ApiStringConstraint]
	public mixed $sort_order = null;

	#[ApiStringConstraint]
	public mixed $sort_by = null;

	#[ApiIntegerConstraint]
	public mixed $limit = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiIntegerConstraint]
	public mixed $workflow_id = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
