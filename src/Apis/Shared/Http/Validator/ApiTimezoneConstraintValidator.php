<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiTimezoneConstraintValidator extends ConstraintValidator
{
	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiTimezoneConstraint) {
			throw new UnexpectedTypeException($constraint, ApiTimezoneConstraint::class);
		}

		if (!empty($value) && !in_array($value, \DateTimeZone::listIdentifiers())) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
