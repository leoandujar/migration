<?php

namespace App\Connector\CustomerPortal\Dto;

class LanguageDto
{
	public string $id;
	public string $name;
	public string $displayName;
	public string $symbol;

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

	public function setDisplayName(string $displayName): self
	{
		$this->displayName = $displayName;

		return $this;
	}

	public function setSymbol(string $symbol): self
	{
		$this->symbol = $symbol;

		return $this;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->id,
			'name'        => $this->name,
			'displayName' => $this->displayName,
			'symbol'      => $this->symbol,
		];
	}
}
