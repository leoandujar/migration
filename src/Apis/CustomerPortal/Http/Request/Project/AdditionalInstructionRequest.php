<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiHtmlConstraint;

class AdditionalInstructionRequest extends ApiRequest
{
	#[ApiHtmlConstraint]
	public mixed $from_customer = null;

	#[ApiHtmlConstraint]
	public mixed $for_provider = null;

	#[ApiHtmlConstraint]
	public mixed $internal = null;

	#[ApiHtmlConstraint]
	public mixed $payment_note_for_customer = null;

	#[ApiHtmlConstraint]
	public mixed $notes = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
