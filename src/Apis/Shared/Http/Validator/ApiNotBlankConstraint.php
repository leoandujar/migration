<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotBlankValidator;

#[\Attribute]
class ApiNotBlankConstraint extends NotBlank
{
	public string $apiCode = ApiError::CODE_MISSING_PARAM;

	public function validatedBy(): string
	{
		return NotBlankValidator::class;
	}

	public function getTargets(): string
	{
		return Constraint::PROPERTY_CONSTRAINT;
	}
}
