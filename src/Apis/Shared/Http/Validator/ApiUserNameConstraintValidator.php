<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiUserNameConstraintValidator extends ConstraintValidator
{
	public static string $pattern =  '/^\S{5,}\z/';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiUserNameConstraint) {
			throw new UnexpectedTypeException($constraint, ApiUserNameConstraint::class);
		}

		if (!empty($value) && !preg_match(self::$pattern, $value)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
