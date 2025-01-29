<?php

namespace App\Apis\AdminPortal\Http\Request\Template;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiFixedValueConstraint;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UpdateTemplateRequest extends ApiRequest
{
	#[ApiIdentifierConstraint]
	public mixed $contact_person_id = null;

	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiFixedValueConstraint]
	public mixed $type = null;

	#[ApiFixedValueConstraint]
	public mixed $target_entity = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $data = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
