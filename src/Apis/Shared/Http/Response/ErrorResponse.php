<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Response;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends ApiResponse
{
	public function __construct(?int $code = null, ?string $internalCode = null, ?string $message = null, ?string $field = null)
	{
		$code         = $code ?: Response::HTTP_INTERNAL_SERVER_ERROR;
		$internalCode = $internalCode ?: ApiError::CODE_INTERNAL_ERROR;
		$message      = $message ?: ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR];
		parent::__construct($field, $code, $internalCode, $message);
	}
}
