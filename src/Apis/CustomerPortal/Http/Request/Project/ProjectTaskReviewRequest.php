<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiHtmlConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class ProjectTaskReviewRequest extends ApiRequest
{
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
