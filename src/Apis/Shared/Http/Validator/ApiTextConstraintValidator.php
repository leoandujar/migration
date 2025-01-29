<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiTextConstraintValidator extends ConstraintValidator
{
	public static string $pattern =  '/[\pL\pN_\-\.]+/i';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiTextConstraint) {
			throw new UnexpectedTypeException($constraint, ApiTextConstraint::class);
		}

		if (!empty($value) && !preg_match(self::$pattern, $value)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
