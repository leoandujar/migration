<?php

namespace App\Apis\AdminPortal\Http\Request\Settings;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class SettingRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIdentifierConstraint]
	public mixed $customer_id = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
