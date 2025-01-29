<?php

namespace App\Apis\Shared\DTO;

class CurrencyDto
{
	public ?string $symbol;
	public ?string $name;

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
