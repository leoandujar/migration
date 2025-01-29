<?php

declare(strict_types=1);

namespace App\Apis\Shared\Http\Request;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Apis\Shared\Facade\AppFacade;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiRequest
{
	#[ApiNotBlankConstraint(groups: ['pagination'])]
	#[ApiIntegerConstraint(groups: ['pagination'])]
	protected mixed $page;

	#[ApiNotBlankConstraint(groups: ['pagination'])]
	#[ApiIntegerConstraint(groups: ['pagination'])]
	protected mixed $per_page;

	#[ApiChoiceConstraint(choices: ['DESC', 'ASC'], groups: ['pagination'])]
	protected mixed $sort_order;

	protected ?ValidatorInterface $validator;
	protected array $constraints = [];
	protected bool $isValid;
	protected array $values = [];
	protected ErrorResponse $error;

	protected array $requiredKeys = [];
	protected array $validators = [];
	protected bool $allowEmpty = true;
	protected bool $enablePagination = false;

	public function __construct(array $values = [])
	{
		$this->validator = AppFacade::getInstance()->validator;
		$this->values = $values;
		$this->isValid = true;
		$this->error = new ErrorResponse();
		$this->checkValues();
		$this->validate();
	}

	protected function checkValues(): void
	{
		foreach ($this->values as $key => $value) {
			if (!property_exists($this, $key) && !$this->allowEmpty) {
				$this->isValid = false;
				$this->error->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->error->updateInternalCode(ApiError::CODE_UNRECOGNIZED_PARAMETER);
				$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_UNRECOGNIZED_PARAMETER]);
				$this->error->updateInvalidParam($key);
				break;
			}
			$this->$key = $value;
		}
	}

	protected function validate(): bool
	{
		if (!$this->isValid) {
			return false;
		}
		if (!count($this->values) && !$this->allowEmpty) {
			$this->isValid = false;
			$this->error->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->error->updateInternalCode(ApiError::CODE_FORBIDDEN_EMPTY_REQUEST);
			$this->error->updateMessage(ApiError::$descriptions[ApiError::CODE_FORBIDDEN_EMPTY_REQUEST]);

			return false;
		}

		$groups = ['Default'];
		if ($this->enablePagination) {
			$groups[] = 'pagination';
		}

		$violations = $this->validator->validate($this, null, $groups);
		if (count($violations) > 0) {
			foreach ($violations as $violation) {
				$this->isValid = false;
				$this->error->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->error->updateInternalCode($violation->getConstraint()->apiCode);
				$this->error->updateMessage($violation->getMessage());
				$this->error->updateInvalidParam($violation->getPropertyPath());
				break;
			}
		}

		return true;
	}

	public function getError(): ErrorResponse
	{
		return $this->error;
	}

	public function isValid(): bool
	{
		return $this->isValid;
	}

	public function getParams(): array
	{
		if ($this->enablePagination) {
			$this->setDefaultSort();
		}

		return $this->values;
	}

	private function setDefaultSort(): void
	{
		$this->values['sort_by'] = $this->values['sort_by'] ?? 'id';
		$this->values['sort_order'] = $this->values['sort_order'] ?? 'ASC';
	}
}
