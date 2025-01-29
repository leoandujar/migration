<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiBooleanConstraintValidator extends ConstraintValidator
{
	private const ALLOWED_DATA = ['TRUE', 'true', 'FALSE', 'false', '1', '0'];

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiBooleanConstraint) {
			throw new UnexpectedTypeException($constraint, ApiBooleanConstraint::class);
		}

		if (!empty($value) && !is_bool($value) && !in_array($value, self::ALLOWED_DATA)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
