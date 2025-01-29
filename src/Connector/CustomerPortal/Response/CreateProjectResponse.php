<?php

namespace App\Connector\CustomerPortal\Response;

use App\Apis\Shared\DTO\ProjectDtoV2;

class CreateProjectResponse extends Response
{
	private ProjectDtoV2 $projectDto;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	/**
	 * @return mixed
	 */
	public function translateRaw(): void
	{
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
				$this->projectDto = new ProjectDtoV2(
					$this->raw['id'],
					$this->raw['idNumber'],
					$this->raw['refNumber'],
					$this->raw['name'],
					$this->raw['service'],
					$this->raw['workflow'],
					$this->raw['specialization'],
					$this->raw['startDate']['formatted'] ?? null,
					$this->raw['deadline']['formatted'] ?? null,
					$this->raw['office']['name'] ?? null,
					$this->raw['customerNotes'],
					$this->raw['status'],
					$this->raw['projectManager']['id'] ?? null,
					$this->raw['isProject'],
					$this->raw['projectConfirmationAvailable']
				);
			}
		}
	}

	public function getProjectDto(): ?ProjectDtoV2
	{
		return $this->projectDto;
	}
}
