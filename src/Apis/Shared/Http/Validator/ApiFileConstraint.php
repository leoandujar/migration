<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Validator;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\FileValidator;

#[\Attribute]
class ApiFileConstraint extends File
{
	public string $apiCode = ApiError::CODE_INVALID_VALUE;

	public function __construct(int|string $maxSize = '1000M', array $mimeTypes = [], array $groups = ['Default'])
	{
		$message = 'The file is invalid.';
		parent::__construct(
			maxSize: $maxSize,
			mimeTypes: $mimeTypes,
			maxSizeMessage: $message,
			groups: $groups
		);
	}

	public function validatedBy(): string
	{
		return FileValidator::class;
	}
}
