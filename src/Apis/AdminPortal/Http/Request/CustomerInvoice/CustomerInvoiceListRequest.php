<?php

namespace App\Apis\AdminPortal\Http\Request\CustomerInvoice;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use Symfony\Component\Validator\Constraints as Assert;


class CustomerInvoiceListRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	public mixed $status = null;

	#[ApiArrayConstraint]
	public mixed $payment_status = null;

	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	#[ApiArrayConstraint]
	public mixed $final_date = null;

	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	#[ApiArrayConstraint]
	public mixed $due_date = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiArrayConstraint]
	public mixed $customer_id = null;

	#[ApiBooleanConstraint]
	public mixed $only_ids = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
