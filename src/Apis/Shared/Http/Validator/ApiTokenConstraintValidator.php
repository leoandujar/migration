<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiTokenConstraintValidator extends ConstraintValidator
{
	public static string $pattern = '/^([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_\-\+\/=]*)+$/i';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiTokenConstraint) {
			throw new UnexpectedTypeException($constraint, ApiTokenConstraint::class);
		}

		if (!empty($value) && !preg_match(self::$pattern, $value)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
