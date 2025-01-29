<?php

namespace App\Apis\CustomerPortal\Http\Request\Project;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class ProjectSubmitExtraFilesRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $input_files = null;

	#[ApiArrayConstraint]
	public mixed $reference_files = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
