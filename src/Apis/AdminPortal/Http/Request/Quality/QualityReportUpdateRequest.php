<?php

namespace App\Apis\AdminPortal\Http\Request\Quality;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class QualityReportUpdateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $status = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
