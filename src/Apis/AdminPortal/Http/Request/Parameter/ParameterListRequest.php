<?php

namespace App\Apis\AdminPortal\Http\Request\Parameter;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ParameterListRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $scope = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
