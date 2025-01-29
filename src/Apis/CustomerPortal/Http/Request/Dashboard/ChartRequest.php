<?php

namespace App\Apis\CustomerPortal\Http\Request\Dashboard;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiDateConstraint;
use App\Apis\Shared\Http\Validator\ApiDateIdConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class ChartRequest extends ApiRequest
{
	#[ApiDateIdConstraint]
	public mixed $relative_date = null;

	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiNotBlankConstraint(),
		new ApiDateConstraint(),
	])]
	#[ApiCountConstraint(
		min: 2,
		max: 2,
	)]
	public mixed $between = null;

	#[ApiArrayConstraint]
	public mixed $filters = null;

	#[ApiChoiceConstraint(choices: ['month', 'quarter', 'year'])]
	protected mixed $group_by = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
