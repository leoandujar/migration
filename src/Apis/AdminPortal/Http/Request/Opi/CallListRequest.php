<?php

namespace App\Apis\AdminPortal\Http\Request\Opi;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class CallListRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $customer_id = null;

	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	#[ApiArrayConstraint]
	public mixed $start_date = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
