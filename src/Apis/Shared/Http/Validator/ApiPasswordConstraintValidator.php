<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiPasswordConstraintValidator extends ConstraintValidator
{
	public static string $pattern = '/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiPasswordConstraint) {
			throw new UnexpectedTypeException($constraint, ApiPasswordConstraint::class);
		}

		if (!empty($value) && !preg_match(self::$pattern, $value)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
