<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Request;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Http\Validator\PatternValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiFileRequest extends ApiRequest
{
	protected int $allowSizeMB = 5000;
	protected array $allowMimes = [];

	/**
	 * @return void
	 */
	protected function validate(): bool
	{
		$this->error = new ErrorResponse();
		$dataSent = array_keys($this->values);
		$requiredKeys = array_keys($this->requiredKeys);

		if (!count($this->values) && !$this->allowEmpty) {
			$this->isValid = false;
			$this->error->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->error->updateInternalCode(ApiError::CODE_FORBIDEN_EMPTY_REQUEST);
			$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_FORBIDEN_EMPTY_REQUEST]);

			return false;
		}

		foreach ($requiredKeys as $requiredKey) {
			if (!in_array($requiredKey, $dataSent)) {
				$this->isValid = false;
				$errorCode = $this->requiredKeys[$requiredKey];
				$this->error->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->error->updateInternalCode($errorCode);
				$this->error->updateMessage(ApiError::$descriptions[$errorCode]);

				return false;
			}
		}

		foreach ($this->values as $field => $value) {
			if (!empty($this->validators) && !isset($this->validators[$field])) {
				continue;
			}

			if (!empty($this->validators)) {
				/** @var PatternValidator $validator */
				$validator = $this->validators[$field];
				if (!$validator::validate($value)) {
					$this->isValid = false;
					$this->error->setStatusCode(Response::HTTP_BAD_REQUEST);
					$this->error->updateInternalCode($validator::getFailedInternalCode());
					$this->error->updateMessage($validator::getReasonFailed());

					return false;
				}
			}

			if ($value instanceof UploadedFile) {
				$size = number_format($value->getSize() / 1048576, 1);
				$mimeType = $value->getMimeType();

				if ($size > $this->allowSizeMB) {
					$this->isValid = false;
					$this->error->updateInternalCode(ApiError::CODE_ERROR_FILE_TOO_BIG);
					$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_ERROR_FILE_TOO_BIG]);

					return false;
				}
				if (count($this->allowMimes) && !in_array($mimeType, $this->allowMimes)) {
					$this->isValid = false;
					$this->error->updateInternalCode(ApiError::CODE_INVALID_VALUE);
					$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'file');

					return false;
				}
			}
		}

		return true;
	}
}
