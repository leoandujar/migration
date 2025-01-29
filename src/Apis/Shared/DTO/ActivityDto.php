<?php

namespace App\Apis\Shared\DTO;

class ActivityDto
{
	public string $id;
	public string $idNumber;
	public string $provider;

	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setIdNumber(string $idNumber): self
	{
		$this->idNumber = $idNumber;

		return $this;
	}

	public function setProvider(string $provider): self
	{
		$this->provider = $provider;

		return $this;
	}
}
