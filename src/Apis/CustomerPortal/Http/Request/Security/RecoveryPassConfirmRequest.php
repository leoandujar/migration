<?php

namespace App\Apis\CustomerPortal\Http\Request\Security;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class RecoveryPassConfirmRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	public mixed $token = null;

	#[ApiNotBlankConstraint]
	public mixed $new_password = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
