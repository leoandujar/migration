<?php

namespace App\Apis\CustomerPortal\Http\Request\Invoice;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class InvoiceExportRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiIntegerConstraint(),
	])]
	public mixed $invoiceIds = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
