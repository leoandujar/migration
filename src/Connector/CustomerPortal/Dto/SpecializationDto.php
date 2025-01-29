<?php

namespace App\Connector\CustomerPortal\Dto;

class SpecializationDto
{
	public string $id;
	public string $name;
	public string $localizedName;

	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function setLocalizedName(string $localizedName): self
	{
		$this->localizedName = $localizedName;

		return $this;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->id,
			'name'          => $this->name,
			'localizedName' => $this->localizedName,
		];
	}
}
