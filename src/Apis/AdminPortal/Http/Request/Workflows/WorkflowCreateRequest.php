<?php

namespace App\Apis\AdminPortal\Http\Request\Workflows;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiCronConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiTextConstraint;
use App\Apis\Shared\Http\Validator\ApiUrlConstraint;
use Symfony\Component\Validator\Constraints\NotNull;

class WorkflowCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiTextConstraint]
	public mixed $description = null;

	#[ApiIntegerConstraint]
	public mixed $workflow_type = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $params = null;

	#[ApiUrlConstraint]
	public mixed $notification_target = null;

	#[NotNull]
	#[ApiBooleanConstraint]
	public mixed $run_automatically = null;

	#[ApiCronConstraint]
	public mixed $run_pattern = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $notification_type = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
