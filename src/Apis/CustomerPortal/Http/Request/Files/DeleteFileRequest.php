<?php

namespace App\Apis\CustomerPortal\Http\Request\Files;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiIdentifierConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class DeleteFileRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIdentifierConstraint]
	public mixed $file_id = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
