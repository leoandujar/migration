<?php

namespace App\Apis\AdminPortal\Http\Request\ContactPerson;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CreateOneTimeLoginRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $role = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $contact_person_id = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
