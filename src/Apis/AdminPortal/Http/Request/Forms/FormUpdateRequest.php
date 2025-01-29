<?php

namespace App\Apis\AdminPortal\Http\Request\Forms;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiNameConstraint;

class FormUpdateRequest extends ApiRequest
{
	#[ApiNameConstraint]
	public mixed $name = null;

	#[ApiArrayConstraint]
	public mixed $approvers_id = null;

	#[ApiBooleanConstraint]
	public mixed $category = null;

	#[ApiStringConstraint]
	public mixed $template_id = null;

	#[ApiStringConstraint]
	public mixed $pmk_template_id = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
