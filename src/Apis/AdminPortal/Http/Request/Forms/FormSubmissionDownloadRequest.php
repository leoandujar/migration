<?php

namespace App\Apis\AdminPortal\Http\Request\Forms;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class FormSubmissionDownloadRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $ids = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
