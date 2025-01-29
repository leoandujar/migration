<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class ProjectAdditionalContactRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $primary_id = null;

	#[ApiIntegerConstraint]
	public mixed $send_back_to_id = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $contact_persons = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
