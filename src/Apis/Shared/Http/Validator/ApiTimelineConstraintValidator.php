<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiTimelineConstraintValidator extends ConstraintValidator
{
	public const TIMELINE_MONTH   = 'month';
	public const TIMELINE_QUARTER = 'quarter';
	public const TIMELINE_YEAR    = 'year';

	/**
	 * @var array
	 */
	private static $allowedValues = [self::TIMELINE_MONTH, self::TIMELINE_QUARTER, self::TIMELINE_YEAR];

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiTimelineConstraint) {
			throw new UnexpectedTypeException($constraint, ApiTimelineConstraint::class);
		}

		if (!empty($value) && !in_array($value, self::$allowedValues)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
