<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[\Attribute]
class ApiPasswordConstraint extends Constraint
{
	public string $message = 'The value sent is not a valid password.';
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
