<?php

namespace App\Apis\AdminPortal\Http\Request\Quality;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiTextConstraint;

class QualityEvaluationRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $evaluatee_id = null;

	#[ApiNotBlankConstraint]
	#[ApiChoiceConstraint(choices: ['EPC', 'EPM'])]
	protected mixed $type;

	#[ApiTextConstraint]
	public mixed $comment = null;

	#[ApiArrayConstraint]
	public mixed $records = null;

	#[ApiBooleanConstraint]
	public mixed $excellent = null;

	public function __construct(array $params)
	{
		$this->allowEmpty = true;
		parent::__construct($params);
	}
}
