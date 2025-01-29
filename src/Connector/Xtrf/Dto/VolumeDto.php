<?php

namespace App\Connector\Xtrf\Dto;

class VolumeDto
{
	public ?int $unitId;
	public ?float $value;

	public function setUnitId(int $unitId): void
	{
		$this->unitId = $unitId;
	}

	public function setValue(float $value): void
	{
		$this->value = $value;
	}
}
