<?php

namespace App\Connector\CustomerPortal\Dto;

class PriceprofileDto
{
	public string $id;
	public string $name;

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

	public function toArray(): array
	{
		return [
			'id'   => $this->id,
			'name' => $this->name,
		];
	}
}
