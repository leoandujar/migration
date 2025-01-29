<?php

namespace App\Apis\AdminPortal\Http\Request\Forms;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

class FormSubmissionSearchRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiIntegerConstraint]
	public mixed $form_id = null;

	#[ApiIntegerConstraint]
	public mixed $appoved_by = null;

	#[ApiArrayConstraint]
	public mixed $approver_ids = null;

	#[ApiIntegerConstraint]
	public mixed $submitted_by = null;

	#[ApiBooleanConstraint]
	public mixed $related = null;

	#[ApiArrayConstraint]
	public mixed $status = null;

	#[ApiStringConstraint]
	public mixed $sort_order = null;

	#[ApiStringConstraint]
	public mixed $sort_by = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		$this->enablePagination = true;
		parent::__construct($values);
	}
}
