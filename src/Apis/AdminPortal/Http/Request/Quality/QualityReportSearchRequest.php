<?php

namespace App\Apis\AdminPortal\Http\Request\Quality;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class QualityReportSearchRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $search = null;

	#[ApiArrayConstraint]
	public mixed $status = null;

	#[ApiStringConstraint]
	public mixed $type = null;

	#[ApiChoiceConstraint(choices: ['id', 'createdAt'], groups: ['pagination'])]
	protected mixed $sort_by;

	public function __construct(array $values)
	{
		$this->allowEmpty = true;
		$this->enablePagination = true;
		parent::__construct($values);
	}
}
