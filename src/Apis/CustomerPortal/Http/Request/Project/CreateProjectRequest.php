<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CreateProjectRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $service_id = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $source_language_id = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $target_languages_ids = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $specialization_id = null;

	#[ApiNotBlankConstraint]
	#[ApiDateConstraint]
	public mixed $deadline = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiIntegerConstraint]
	public mixed $price_profile = null;

	#[ApiIntegerConstraint]
	public mixed $customer_id = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $input_files = null;

	#[ApiArrayConstraint]
	public mixed $reference_files = null;

	#[ApiArrayConstraint]
	public mixed $instructions = null;

	#[ApiArrayConstraint]
	public mixed $custom_fields = null;

	#[ApiArrayConstraint]
	public mixed $additional_contacts = null;

	#[ApiArrayConstraint]
	public mixed $send_back_to = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
