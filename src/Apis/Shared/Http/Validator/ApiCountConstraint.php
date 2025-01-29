<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\CountValidator;

#[\Attribute]
class ApiCountConstraint extends Count
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function __construct(?int $min = null, ?int $max = null)
	{

		parent::__construct(
			min: $min,
			max: $max,
		);
	}

	public function validatedBy(): string
	{
		return CountValidator::class;
	}
}
