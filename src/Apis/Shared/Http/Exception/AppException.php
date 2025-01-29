<?php

namespace App\Apis\Shared\Http\Exception;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\Response;

class AppException extends \Error
{
	protected $code;
	protected $message;
	protected ?string $internalCode;
	protected ErrorResponse $errorResponse;

	public function __construct(int $code = null, string $internalCode = null, string $message = null)
	{
		$code = $code ?: Response::HTTP_INTERNAL_SERVER_ERROR;
		$internalCode = $internalCode ?: ApiError::CODE_INTERNAL_ERROR;
		$message = $message ?: ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR];
		$this->errorResponse = new ErrorResponse($code, $internalCode, $message);
		parent::__construct($message, $code);
	}

	public function getInternalCode(): ?string
	{
		return $this->internalCode;
	}

	public function getErrorResponse(): ErrorResponse
	{
		return $this->errorResponse;
	}
}
