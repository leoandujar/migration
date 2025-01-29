<?php

namespace App\Apis\CustomerPortal\Http\Request\General;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class DefaultEstimateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $total_words = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $target_languages = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $source_language = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
