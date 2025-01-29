<?php

namespace App\Apis\AdminPortal\Http\Request\Workflows;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;

class WorkflowRunRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	public mixed $params = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = true;
		parent::__construct($values);
	}
}
