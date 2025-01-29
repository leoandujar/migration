<?php

namespace App\Apis\CustomerPortal\Http\Request\Utils;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNameConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class NotifyPmEmailRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $type = null;

	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $entity_id = null;

	#[ApiNotBlankConstraint]
	#[ApiNameConstraint]
	public mixed $entity_name = null;

	#[ApiNotBlankConstraint]
	#[ApiNameConstraint]
	public mixed $function_name = null;

	#[ApiStringConstraint]
	public mixed $template = null;

	#[ApiNotBlankConstraint]
	#[ApiStringConstraint]
	public mixed $subject = null;

	#[ApiNotBlankConstraint]
	#[ApiArrayConstraint]
	public mixed $variables = null;
}
