<?php

namespace App\Apis\AdminPortal\Http\Request\Quality;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class QualityReportScoreRequest extends ApiRequest
{
	#[ApiBooleanConstraint]
	public mixed $excellent = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
