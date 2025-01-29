<?php

namespace App\Apis\AdminPortal\Http\Request\Account;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiPasswordConstraint;

class AccountPasswordChangeRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiPasswordConstraint]
	public mixed $old_password = null;

	#[ApiNotBlankConstraint]
	#[ApiPasswordConstraint]
	public mixed $new_password = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
