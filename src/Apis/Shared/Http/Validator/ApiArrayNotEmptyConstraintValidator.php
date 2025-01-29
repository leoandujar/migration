<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiArrayNotEmptyConstraintValidator extends ConstraintValidator
{
	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiArrayNotEmptyConstraint) {
			throw new UnexpectedTypeException($constraint, ApiArrayNotEmptyConstraint::class);
		}

		if (empty($value) || !is_array($value)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
