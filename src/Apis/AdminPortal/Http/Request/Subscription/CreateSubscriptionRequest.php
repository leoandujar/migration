<?php

namespace App\Apis\AdminPortal\Http\Request\Subscription;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiUrlConstraint;

class CreateSubscriptionRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiUrlConstraint]
	public mixed $url = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $event = null;

	#[ApiArrayConstraint]
	public mixed $filter = null;

	#[ApiStringConstraint]
	public mixed $embed = null;

	public function __construct(array $params)
	{
		$this->allowEmpty = false;

		parent::__construct($params);
	}
}
