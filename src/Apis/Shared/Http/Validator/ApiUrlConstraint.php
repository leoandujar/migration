<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraints\UrlValidator;
use Symfony\Component\Validator\Constraints\Url;

#[\Attribute]
class ApiUrlConstraint extends Url
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function validatedBy(): string
	{
		return UrlValidator::class;
	}
}
