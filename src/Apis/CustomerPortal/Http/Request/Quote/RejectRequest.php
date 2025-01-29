<?php

namespace App\Apis\CustomerPortal\Http\Request\Quote;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class RejectRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $reason_id = null;

	#[ApiStringConstraint]
	public mixed $comment = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
