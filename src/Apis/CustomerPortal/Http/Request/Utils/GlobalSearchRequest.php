<?php

namespace App\Apis\CustomerPortal\Http\Request\Utils;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiFixedValueConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class GlobalSearchRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	public mixed $internal_status = null;

	#[ApiArrayConstraint]
	public mixed $status = null;

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
    #[ApiNotBlankConstraint]
	public mixed $search = null;

	#[ApiStringConstraint]
	public mixed $customer_project_number = null;

	#[ApiArrayConstraint]
	#[Assert\All([
        new ApiNotBlankConstraint(),
        new ApiDateConstraint(),
    ])]
    #[ApiCountConstraint(
        min: 2,
        max: 2,
    )]
	public mixed $start_date = null;

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
	public mixed $office = null;

	#[ApiArrayConstraint]
	public mixed $requested_by = null;

	#[ApiFixedValueConstraint]
	public mixed $survey_status = null;

	#[ApiArrayConstraint]
	public mixed $source_languages = null;

	public function __construct(array $values)
	{
		$this->enablePagination = true;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
