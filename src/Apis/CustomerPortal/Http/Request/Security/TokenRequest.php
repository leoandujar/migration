<?php

namespace App\Apis\CustomerPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiShortCodeConstraint;

class TokenRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiShortCodeConstraint]
	public mixed $token = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
