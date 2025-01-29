<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CancelProjectRequest extends ApiRequest
{
	#[ApiStringConstraint]
	public mixed $reason = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
