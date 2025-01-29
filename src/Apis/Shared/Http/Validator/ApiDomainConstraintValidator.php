<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiDomainConstraintValidator extends ConstraintValidator
{
	public static string $pattern = '/^(http[s]?\:\/\/)?((\w+)\.)?(([\w-]+)?)(\.[\w-]+){1,2}$/i';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiDomainConstraint) {
			throw new UnexpectedTypeException($constraint, ApiDomainConstraint::class);
		}

		if (!empty($value) && (!is_string($value) || !preg_match(self::$pattern, $value))) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
