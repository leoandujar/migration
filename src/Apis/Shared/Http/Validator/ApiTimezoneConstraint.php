<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[\Attribute]
class ApiTimezoneConstraint extends Constraint
{
	public string $message = 'The value is not valid timezone.';
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function validatedBy(): string
	{
		return static::class.'Validator';
	}

	public function getTargets(): string
	{
		return Constraint::PROPERTY_CONSTRAINT;
	}
}
