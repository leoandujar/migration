<?php

namespace App\Apis\AdminPortal\Http\Request\Template;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiFixedValueConstraint;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;

class GetTemplateRequest extends ApiRequest
{
	#[ApiIdentifierConstraint]
	public mixed $internal_user_id = null;

	#[ApiFixedValueConstraint]
	public mixed $target_entity = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
