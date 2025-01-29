<?php

namespace App\Apis\Shared\Http\Validator;

use App\Model\Entity\CPTemplate;
use App\Model\Entity\Permission;
use App\Model\Entity\Project;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApiFixedValueConstraintValidator extends ConstraintValidator
{
	private static array $values = [
		Permission::TARGET_ADMIN_PORTAL,
		Permission::TARGET_CLIENT_PORTAL,
		CPTemplate::TYPE_CONTACT_PERSON,
		CPTemplate::TYPE_CUSTOMER,
		CPTemplate::TARGET_ENTITY_PROJECT,
		CPTemplate::TARGET_ENTITY_QUOTE,
		Project::SURVEY_ANY,
		Project::SURVEY_NOT_SURVEYED,
		Project::SURVEY_SURVEYED,
	];

	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof ApiFixedValueConstraint) {
			throw new UnexpectedTypeException($constraint, ApiFixedValueConstraint::class);
		}

		if (!empty($value) && !in_array($value, static::$values)) {
			$this->context->buildViolation($constraint->message)
				->setCode($constraint->apiCode)
				->addViolation();
		}
	}
}
