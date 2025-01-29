<?php

namespace App\Apis\CustomerPortal\Http\Request\Files;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiFileConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class UploadFileRequest extends ApiRequest
{
	#[ApiNotBlankConstraint]
	#[ApiFileConstraint(
		maxSize: '2048M'
	)]
	public mixed $file = null;
}
