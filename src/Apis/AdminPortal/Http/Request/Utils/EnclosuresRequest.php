<?php

namespace App\Apis\AdminPortal\Http\Request\Utils;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class EnclosuresRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $path = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
