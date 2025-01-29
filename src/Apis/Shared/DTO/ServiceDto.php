<?php

namespace App\Apis\Shared\DTO;

class ServiceDto
{
	public function __construct(
		public string $id,
		public string $name,
		public ?string $type
	) {
	}
}
