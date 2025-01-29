<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\TypeValidator;

#[\Attribute]
class ApiFloatConstraint extends Type
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function __construct(?array $groups = null)
	{
		parent::__construct(['float'], null, $groups);
	}

	public function validatedBy(): string
	{
		return TypeValidator::class;
	}
}
