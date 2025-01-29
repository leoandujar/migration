<?php

namespace App\Apis\Shared\DTO;

class RoleActionDto
{
	public function __construct(
		public string $id,
		public string $code,
		public ?string $name,
		public int $target
	) {
	}
}
