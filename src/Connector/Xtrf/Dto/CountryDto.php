<?php

namespace App\Connector\Xtrf\Dto;

class CountryDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?string $localizedName,
		public ?string $symbol,
	) {
	}
}
