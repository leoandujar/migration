<?php

namespace App\Apis\CustomerPortal\Http\Request\Customer;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiEmailConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UpdateCustomerRequest extends ApiRequest
{
	#[ApiBooleanConstraint]
	public mixed $use_address_as_correspondence = null;

	#[ApiEmailConstraint]
	public mixed $email = null;

	#[ApiStringConstraint]
	public mixed $name = null;

	#[ApiIntegerConstraint]
	public mixed $address_country = null;

	#[ApiIntegerConstraint]
	public mixed $address_province = null;

	#[ApiStringConstraint]
	public mixed $address_city = null;

	#[ApiStringConstraint]
	public mixed $address_zip_code = null;

	#[ApiStringConstraint]
	public mixed $address_address = null;

	#[ApiStringConstraint]
	public mixed $address_address2 = null;

	#[ApiIntegerConstraint]
	public mixed $correspondence_country = null;

	#[ApiIntegerConstraint]
	public mixed $correspondence_province = null;

	#[ApiStringConstraint]
	public mixed $correspondence_city = null;

	#[ApiStringConstraint]
	public mixed $correspondence_zip_code = null;

	#[ApiStringConstraint]
	public mixed $correspondence_address = null;

	#[ApiStringConstraint]
	public mixed $correspondence_address2 = null;

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

	#[ApiStringConstraint]
	public mixed $www = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
