<?php

namespace App\Apis\CustomerPortal\Http\Request\Member;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UpdateMemberRequest extends ApiRequest
{
	#[ApiEmailConstraint]
	public mixed $email = null;

	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiStringConstraint]
	public mixed $last_name = null;

	#[ApiStringConstraint]
	public mixed $phone = null;

	#[ApiStringConstraint]
	public mixed $mobile_phone = null;

	#[ApiStringConstraint]
	public mixed $address_phone2 = null;

	#[ApiStringConstraint]
	public mixed $address_phone3 = null;

	#[ApiStringConstraint]
	public mixed $fax = null;

	#[ApiStringConstraint]
	public mixed $person_position = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
