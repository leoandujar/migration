<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeValidator;

#[\Attribute]
class ApiDateConstraint extends DateTime
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function __construct(string $format = 'm/d/Y')
	{
		parent::__construct(
			format: $format,
		);
	}

	public function validatedBy(): string
	{
		return DateTimeValidator::class;
	}

	public function getTargets(): string
	{
		return Constraint::PROPERTY_CONSTRAINT;
	}
}
