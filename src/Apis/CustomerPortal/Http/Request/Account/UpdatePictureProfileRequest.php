<?php

namespace App\Apis\CustomerPortal\Http\Request\Account;

use App\Apis\Shared\Http\Request\ApiFileRequest;
use App\Apis\Shared\Http\Validator\ApiFileConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;

class UpdatePictureProfileRequest extends ApiFileRequest
{
	#[ApiNotBlankConstraint]
	#[ApiFileConstraint(
		maxSize: '3M',
		mimeTypes: ['image/png', 'image/jpeg']
	)]
	public mixed $picture = null;
}
