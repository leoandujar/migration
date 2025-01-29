<?php

namespace App\Apis\CustomerPortal\Http\Request\Invoice;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class InvoiceCheckoutRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiIntegerConstraint(),
	])]
	public mixed $invoice_ids = null;

	#[ApiStringConstraint]
	public mixed $description = null;

	#[ApiStringConstraint]
	public mixed $reference = null;

	#[ApiStringConstraint]
	public mixed $path = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
