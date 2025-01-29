<?php

namespace App\Apis\Shared\DTO;

class SimpleObjDto
{
	public function __construct(
		public ?string $value,
		public ?string $label
	) {
	}
}
