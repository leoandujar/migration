<?php

namespace App\Apis\Shared\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiCronConstraintValidator extends ConstraintValidator
{
	public static string $pattern =  '/^(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?))(,(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?)))*\s(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?))(,(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?)))*\s(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?))(,(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?)))*\s(\?|(((\*|(\d\d?L?))(\/\d\d?)?)|(\d\d?L?\-\d\d?L?)|L|(\d\d?W))(,(((\*|(\d\d?L?))(\/\d\d?)?)|(\d\d?L?\-\d\d?L?)|L|(\d\d?W)))*)\s(((\*|(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))(\/\d\d?)?)|((\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\-(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)))(,(((\*|(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))(\/\d\d?)?)|((\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\-(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))))*\s(((\*|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)(\/\d\d?)?)|(([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?\-([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?#([1-5]))(,(((\*|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)(\/\d\d?)?)|(([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?\-([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?#([1-5])))*$/i';

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiCronConstraint) {
			throw new UnexpectedTypeException($constraint, ApiCronConstraint::class);
		}

		if (!empty($value) && (!is_string($value) || !preg_match(self::$pattern, $value))) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
