<?php

namespace App\Apis\CustomerPortal\Http\Request\Invoice;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class InvoiceCheckoutStatusRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $session_id = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
