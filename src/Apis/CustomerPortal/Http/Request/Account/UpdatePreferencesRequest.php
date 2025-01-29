<?php

namespace App\Apis\CustomerPortal\Http\Request\Account;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiTimezoneConstraint;

class UpdatePreferencesRequest extends ApiRequest
{
	#[ApiTimezoneConstraint]
	public mixed $timezone;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
