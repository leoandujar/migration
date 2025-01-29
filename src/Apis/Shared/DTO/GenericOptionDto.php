<?php

namespace App\Apis\Shared\DTO;

class GenericOptionDto
{
	public function __construct(
		public ?int $value,
		public ?string $label
	) {
	}
}
