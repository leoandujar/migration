<?php

namespace App\Apis\AdminPortal\Http\Request\Permissions;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;

class PermissionUpdateRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $permissions = null;

	public function __construct(array $values)
	{
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
