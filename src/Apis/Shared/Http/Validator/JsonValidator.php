<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class JsonValidator extends ConstraintValidator
{
	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof Json) {
			throw new UnexpectedTypeException($constraint, Json::class);
		}

		if (null === $value || '' === $value) {
			return;
		}

		if (\is_scalar($value) && !$value instanceof \Stringable && !is_array($value)) {
			$this->context->buildViolation($constraint->message)
				->setParameter('{{ value }}', $this->formatValue($value))
				->setCode(Json::INVALID_JSON_ERROR)
				->addViolation();

			return;
		}

		if (is_array($value)) {
			$value = json_encode($value);
		}

		$value = (string) $value;

		if (!json_validate($value)) {
			$this->context->buildViolation($constraint->message)
				->setParameter('{{ value }}', $this->formatValue($value))
				->setCode(Json::INVALID_JSON_ERROR)
				->addViolation();
		}
	}
}
