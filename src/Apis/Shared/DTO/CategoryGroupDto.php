<?php

namespace App\Apis\Shared\DTO;

class CategoryGroupDto
{
	public function __construct(
		public ?string $id,
		public string $name,
		public string $code,
		public int $target,
		public bool $active,
		public ?array $charts,
		public ?array $workflows,
	) {
	}
}
