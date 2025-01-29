<?php

namespace App\Apis\CustomerPortal\Http\Request\Quote;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuoteRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	public mixed $status = null;

	#[ApiStringConstraint]
	public mixed $customer_project_number = null;

	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	public mixed $requested_on = null;

	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	public mixed $deadline = null;

	#[ApiArrayConstraint]
	public mixed $target_languages = null;

	#[ApiArrayConstraint]
	public mixed $source_languages = null;

	#[ApiArrayConstraint]
	public mixed $offices = null;

	#[ApiArrayConstraint]
	public mixed $services = null;

	#[ApiArrayConstraint]
	public mixed $requested_by = null;

    #[ApiChoiceConstraint(choices: ['id', 'status', 'name', 'service'], groups: ['pagination'])]
    protected mixed $sort_by;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
