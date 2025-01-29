<?php

namespace App\Apis\CustomerPortal\Http\Request\Member;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class UpdateMemberStatusRequest extends ApiRequest
{
	#[ApiBooleanConstraint]
	public mixed $active;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
