<?php

namespace App\Connector\Xtrf\Response\Projects;

use App\Connector\Xtrf\Dto\ProjectDto;
use App\Connector\Xtrf\Response\Response;

class CreateProjectResponse extends Response
{
	private ?ProjectDto $project = null;

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
			$this->project = new ProjectDto();
			$this->project
				->setId($this->raw['id'])
				->setProjectId($this->raw['projectId'])
				->setIsClassicProject($this->raw['isClassicProject'])
				->setIdNumber($this->raw['idNumber'])
				->setName($this->raw['name'])
				->setCategoryIds($this->raw['categoriesIds'])
				->setCustomerId($this->raw['customerId'])
				->setContactPersonId($this->raw['contactPersonId'])
				->setFinance($this->raw['finance'])
				->setCustomFields($this->raw['customFields'])
				->setInstructions($this->raw['instructions'])
				->setProjectManagerId($this->raw['projectManagerId'])
				->setStatus($this->raw['status'])
				->setSpecializationId($this->raw['specializationId'])
				->setDates($this->raw['dates'])
				->setContacts($this->raw['contacts']);
		}
	}

	public function getProject(): ?ProjectDto
	{
		return $this->project;
	}
}
