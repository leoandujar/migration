<?php

namespace App\Apis\Shared\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiTokenConstraint;

class RefreshTokenRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiTokenConstraint]
	public mixed $token;

	public function __construct(array $params)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($params);
	}
}
