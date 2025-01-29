<?php

namespace App\Apis\Shared\DTO;

class LanguageDto
{
	public string $id;
	public string $symbol;
	public string $name;

	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setSymbol(string $symbol): self
	{
		$this->symbol = $symbol;

		return $this;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}
}
