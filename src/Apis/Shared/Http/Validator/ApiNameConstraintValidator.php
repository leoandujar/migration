<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiNameConstraintValidator extends ConstraintValidator
{
	public static string $pattern =  '/^[a-zA-Z.,\'’ ]{1,60}+$/i';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiNameConstraint) {
			throw new UnexpectedTypeException($constraint, ApiNameConstraint::class);
		}

		if (!empty($value) && (!is_string($value) || !preg_match(self::$pattern, $value))) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
