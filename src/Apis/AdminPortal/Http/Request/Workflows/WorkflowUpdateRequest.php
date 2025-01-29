<?php

namespace App\Apis\AdminPortal\Http\Request\Workflows;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiCronConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiTextConstraint;
use App\Apis\Shared\Http\Validator\ApiUrlConstraint;
use Symfony\Component\Validator\Constraints\NotNull;

class WorkflowUpdateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiTextConstraint]
	public mixed $description = null;

	#[ApiIntegerConstraint]
	public mixed $workflow_type = null;

	#[ApiArrayConstraint]
	public mixed $params = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiUrlConstraint]
	public mixed $notification_target = null;

	#[NotNull]
	#[ApiBooleanConstraint]
	public mixed $run_automatically = null;

	#[ApiCronConstraint]
	public mixed $run_pattern = null;

	#[ApiIntegerConstraint]
	public mixed $notification_type = null;

	#[ApiArrayConstraint]
	public mixed $category_groups = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
