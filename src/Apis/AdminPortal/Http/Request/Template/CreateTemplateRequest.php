<?php

namespace App\Apis\AdminPortal\Http\Request\Template;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiFixedValueConstraint;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CreateTemplateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIdentifierConstraint]
	public mixed $internal_user_id = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiFixedValueConstraint]
	public mixed $type = null;

	#[ApiNotBlankConstraint]
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
