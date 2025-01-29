<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiDateIdConstraintValidator extends ConstraintValidator
{
	public const DATE_THIS_YEAR    = 'this_year';
	public const DATE_LAST_YEAR    = 'last_year';
	public const DATE_THIS_QUARTER = 'this_quarter';
	public const DATE_LAST_QUARTER = 'last_quarter';

	/**
	 * @var array
	 */
	private static $allowedValues = [self::DATE_THIS_YEAR, self::DATE_LAST_YEAR, self::DATE_THIS_QUARTER, self::DATE_LAST_QUARTER];

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiDateIdConstraint) {
			throw new UnexpectedTypeException($constraint, ApiDateIdConstraint::class);
		}

		if (!empty($value) && !in_array($value, self::$allowedValues)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
