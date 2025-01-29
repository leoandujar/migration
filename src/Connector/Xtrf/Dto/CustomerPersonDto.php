<?php

namespace App\Connector\Xtrf\Dto;

class CustomerPersonDto
{
	public ?string $id;
	public ?string $name;
	public ?string $lastName;
	public ?PersonContactDto $contact;
	public ?string $positionId;
	public ?bool $active;
	public ?string $customerId;

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function setLastName(?string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function setContact(?PersonContactDto $contact): self
	{
		$this->contact = $contact;

		return $this;
	}

	public function setPositionId(?string $positionId): self
	{
		$this->positionId = $positionId;

		return $this;
	}

	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function setCustomerId(?string $customerId): self
	{
		$this->customerId = $customerId;

		return $this;
	}
}
