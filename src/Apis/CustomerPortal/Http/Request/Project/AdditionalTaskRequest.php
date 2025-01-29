<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;

class AdditionalTaskRequest extends ApiRequest
{
	#[ApiIntegerConstraint]
	public mixed $target_language = null;

	#[ApiIntegerConstraint]
	public mixed $source_language = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
