<?php

namespace App\Connector\Xtrf\Dto;

class ProjectDto
{
	public ?string $id;
	public ?string $projectId;
	public ?bool $isClassicProject;
	public ?string $idNumber;
	public ?string $name;
	public ?array $categoryIds = [];
	public ?int $customerId;
	public ?int $contactPersonId;
	public ?array $finance      = [];
	public ?array $customFields = [];
	public ?array $instructions = [];
	public ?int $projectManagerId;
	public ?string $status;
	public ?int $specializationId;
	public ?array $dates    = [];
	public ?array $contacts = [];
	public ?array $tasks    = [];

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setProjectId(?string $projectId): self
	{
		$this->projectId = $projectId;

		return $this;
	}

	public function setIsClassicProject(?bool $isClassicProject): self
	{
		$this->isClassicProject = $isClassicProject;

		return $this;
	}

	public function setIdNumber(?string $idNumber): self
	{
		$this->idNumber = $idNumber;

		return $this;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function setCategoryIds(?array $categoryIds): self
	{
		$this->categoryIds = $categoryIds;

		return $this;
	}

	public function setCustomerId(?int $customerId): self
	{
		$this->customerId = $customerId;

		return $this;
	}

	public function setContactPersonId(?int $contactPersonId): self
	{
		$this->contactPersonId = $contactPersonId;

		return $this;
	}

	public function setFinance(?array $finance): self
	{
		$this->finance = $finance;

		return $this;
	}

	public function setCustomFields(?array $customFields): self
	{
		$this->customFields = $customFields;

		return $this;
	}

	public function setInstructions(?array $instructions): self
	{
		$this->instructions = $instructions;

		return $this;
	}

	public function setProjectManagerId(?int $projectManagerId): self
	{
		$this->projectManagerId = $projectManagerId;

		return $this;
	}

	public function setStatus(?string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function setSpecializationId(?int $specializationId): self
	{
		$this->specializationId = $specializationId;

		return $this;
	}

	public function setDates(?array $dates): self
	{
		$this->dates = $dates;

		return $this;
	}

	public function setContacts(?array $contacts): self
	{
		$this->contacts = $contacts;

		return $this;
	}

	public function setTasks(array $tasks): self
	{
		$this->tasks = $tasks;

		return $this;
	}
}
