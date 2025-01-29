<?php

namespace App\Apis\Shared\DTO;

class RoleDto
{
	public function __construct(
		public string $id,
		public string $code,
		public ?string $name,
		public int $target,
		public ?array $abilities
	) {
	}
}
