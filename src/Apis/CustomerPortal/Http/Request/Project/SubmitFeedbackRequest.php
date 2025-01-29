<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiHtmlConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class SubmitFeedbackRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiBooleanConstraint]
	public mixed $survey_sent = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $customer_feedback_answers = null;

	#[ApiNotBlankConstraint]
	#[ApiHtmlConstraint]
	public mixed $comment = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
