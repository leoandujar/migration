<?php

namespace App\Apis\CustomerPortal\Http\Request\Invoice;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class GetInvoicesRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	public mixed $internal_status = null;

	#[ApiArrayConstraint]
	public mixed $payment_status = null;

	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	public mixed $final_date = null;

	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	public mixed $due_date = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiArrayConstraint]
	public mixed $offices = null;

	#[ApiArrayConstraint]
	public mixed $requested_by = null;

	#[ApiChoiceConstraint(choices: ['id', 'idNumber', 'dueDate', 'status', 'finalDate', 'totalNetto'], groups: ['pagination'])]
	protected mixed $sort_by;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
