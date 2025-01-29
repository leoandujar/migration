<?php

namespace App\Apis\CustomerPortal\Http\Request\Template;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiFixedValueConstraint;
use App\Apis\Shared\Http\Validator\ApiJsonConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UpdateTemplateRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiFixedValueConstraint]
	public mixed $type = null;

	#[ApiJsonConstraint]
	public mixed $data = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
