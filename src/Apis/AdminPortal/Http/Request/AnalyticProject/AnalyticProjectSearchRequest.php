<?php

namespace App\Apis\AdminPortal\Http\Request\AnalyticProject;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class AnalyticProjectSearchRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiIntegerConstraint]
	public mixed $project_id = null;

	#[ApiIntegerConstraint]
	public mixed $task_id = null;

	#[ApiIntegerConstraint]
	public mixed $activity_id = null;

	#[ApiArrayConstraint]
	public mixed $target_language_tag = null;

	#[ApiArrayConstraint]
	public mixed $target_language = null;

	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiArrayConstraint]
	public mixed $status = null;

	#[ApiArrayConstraint]
	public mixed $processing_status = null;

	#[ApiBooleanConstraint]
	public mixed $ignored = null;

	#[ApiBooleanConstraint]
	public mixed $lqa_allowed = null;

	#[ApiBooleanConstraint]
	public mixed $lqa_processed = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
