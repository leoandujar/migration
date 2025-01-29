<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\CollectionValidator;

#[\Attribute]
class ApiCollectionConstraint extends Collection
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function __construct(array $fields, bool $allowMissingFields = false, bool $allowExtraFields = false, array $groups = ['Default'])
	{
		parent::__construct(
			fields: $fields,
			groups: $groups,
			allowExtraFields: $allowExtraFields,
			allowMissingFields: $allowMissingFields
		);
	}

	public function validatedBy(): string
	{
		return CollectionValidator::class;
	}
}
