<?php

namespace App\Apis\Shared\DTO;

class APTemplateDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?int $targetEntity,
		public ?array $data
	) {
	}
}
