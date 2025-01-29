<?php

namespace App\Apis\Shared\Http\Request;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class LoginRequest extends ApiRequest
{
	private mixed $headers;

	public function __construct(array $params, mixed $headers)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		$this->headers = $headers;
		parent::__construct($params);
	}

	protected function validate(): bool
	{
		$this->error = new ErrorResponse();

		$auth = $this->headers->get('Authorization');
		if (!$auth) {
			$this->isValid = false;
			$this->error->setStatusCode(Response::HTTP_UNAUTHORIZED);
			$this->error->updateInternalCode(ApiError::CODE_AUTHENTICATION_FAILED);
			$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_AUTHENTICATION_FAILED]);

			return false;
		}
		$authValue = explode(' ', $auth);
		if (2 == count($authValue)) {
			$authValue = explode(':', base64_decode($authValue[1]));
		}
		if (2 != count($authValue)) {
			$this->isValid = false;
			$this->error->setStatusCode(Response::HTTP_UNAUTHORIZED);
			$this->error->updateInternalCode(ApiError::CODE_AUTHENTICATION_FAILED);
			$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_AUTHENTICATION_FAILED]);

			return false;
		}
		$this->values = [
			'username' => $authValue[0],
			'password' => $authValue[1],
		];

		return true;
	}
}
