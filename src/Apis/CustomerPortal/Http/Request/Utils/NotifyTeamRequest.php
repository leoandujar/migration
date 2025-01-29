<?php

namespace App\Apis\CustomerPortal\Http\Request\Utils;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiHtmlConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiUrlConstraint;

class NotifyTeamRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiIntegerConstraint]
	public mixed $type = null;

	#[ApiNotBlankConstraint]
	#[ApiHtmlConstraint]
	public mixed $title = null;

	#[ApiUrlConstraint]
	public mixed $target = null;

	#[ApiNotBlankConstraint]
	#[ApiHtmlConstraint]
	public mixed $message = null;
}
