<?php

namespace App\Apis\CustomerPortal\Http\Request\Quote;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiHtmlConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Constant\DateConstant;

class CreateQuoteRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $service = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $source_language = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $specialization = null;

	#[ApiIntegerConstraint]
	public mixed $price_profile = null;

	#[ApiIntegerConstraint]
	public mixed $customer = null;

	#[ApiIntegerConstraint]
	public mixed $send_back_to = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $target_languages = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $input_files = null;

	#[ApiArrayConstraint]
	public mixed $reference_files = null;

	#[ApiArrayConstraint]
	public mixed $custom_fields = null;

	#[ApiArrayConstraint]
	public mixed $instructions = null;

	#[ApiArrayConstraint]
	public mixed $additional_contacts = null;

	#[ApiHtmlConstraint]
	public mixed $notes = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $reference_number = null;

	#[ApiBooleanConstraint]
	public mixed $auto_accept = null;

	#[ApiNotBlankConstraint]
	#[ApiDateConstraint(format: DateConstant::GLOBAL_FORMAT)]
	public mixed $delivery_date = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
