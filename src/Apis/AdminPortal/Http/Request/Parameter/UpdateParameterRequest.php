<?php

namespace App\Apis\AdminPortal\Http\Request\Parameter;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UpdateParameterRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $scope = null;

	#[ApiStringConstraint]
	public mixed $value = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
