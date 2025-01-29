<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiIdentifierConstraintValidator extends ConstraintValidator
{
	public static string $pattern =  '/^[a-zA-Z0-9_-]{1,50}+$/i';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiIdentifierConstraint) {
			throw new UnexpectedTypeException($constraint, ApiIdentifierConstraint::class);
		}

		if (!empty($value) && !preg_match(self::$pattern, strval($value))) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
