<?php

namespace App\Apis\CustomerPortal\Http\Request\Template;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiFixedValueConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class GetTemplateRequest extends ApiRequest
{
	#[ApiFixedValueConstraint]
	public mixed $type = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
