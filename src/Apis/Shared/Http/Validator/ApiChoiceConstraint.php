<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

#[\Attribute]
class ApiChoiceConstraint extends Choice
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function __construct(array $choices, array $groups = ['Default'])
	{
		parent::__construct(
			choices: $choices,
			groups: $groups
		);
	}

	public function validatedBy(): string
	{
		return ChoiceValidator::class;
	}
}
