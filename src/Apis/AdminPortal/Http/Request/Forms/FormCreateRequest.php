<?php

namespace App\Apis\AdminPortal\Http\Request\Forms;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiNameConstraint;

class FormCreateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiNameConstraint]
	public mixed $name = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $approvers_id = null;

	#[ApiBooleanConstraint]
	public mixed $category = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $template_id = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $pmk_template_id = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
