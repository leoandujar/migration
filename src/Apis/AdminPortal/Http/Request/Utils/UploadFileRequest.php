<?php

namespace App\Apis\AdminPortal\Http\Request\Utils;

use App\Apis\Shared\Http\Request\ApiFileRequest;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiFileConstraint;

class UploadFileRequest extends ApiFileRequest
{
	
	#[ApiNotBlankConstraint]
	#[ApiFileConstraint(
		maxSize: '2048M'
	)]
	public mixed $file = null;

	#[ApiStringConstraint]
	public mixed $reference = null;
}
