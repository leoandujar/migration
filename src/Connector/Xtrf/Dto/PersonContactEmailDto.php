<?php

namespace App\Connector\Xtrf\Dto;

class PersonContactEmailDto
{
	public ?string $primary;
	public ?array $additional = [];

	public function setPrimary(?string $primary): self
	{
		$this->primary = $primary;

		return $this;
	}

	public function setAdditional(?array $additional): self
	{
		$this->additional = $additional;

		return $this;
	}
}
