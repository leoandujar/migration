<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiHtmlConstraintValidator extends ConstraintValidator
{
	public static function isValidHtmlString(string $text, ?array $allowedTags = null): bool
	{
		try {
			if (!mb_strlen($text)) {
				return true;
			}
			$config = \HTMLPurifier_Config::createDefault();
			$purifier = new \HTMLPurifier($config);
			$purifier->purify($text);
		} catch (\Throwable $thr) {
			return false;
		}

		return true;
	}

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiHtmlConstraint) {
			throw new UnexpectedTypeException($constraint, ApiHtmlConstraint::class);
		}

		if (!empty($value) && (!is_string($value) || !self::isValidHtmlString($value))) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
