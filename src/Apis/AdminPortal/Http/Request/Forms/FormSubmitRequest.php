<?php

namespace App\Apis\AdminPortal\Http\Request\Forms;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

class FormSubmitRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $form_id = null;

	#[ApiIntegerConstraint]
	public mixed $owner = null;

	#[ApiArrayConstraint]
	public mixed $collaborators = null;

	#[ApiBooleanConstraint]
	public mixed $require_approval = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $fields = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
