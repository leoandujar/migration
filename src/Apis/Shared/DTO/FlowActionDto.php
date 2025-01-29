<?php

namespace App\Apis\Shared\DTO;

class FlowActionDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?string $slug,
		public ?string $description,
		public ?string $action,
		public ?array $inputs,
		public ?string $category = 'Generic',
		public ?string $next,
		public ?array $outputs,
		public ?array $inputsOnStart,
	) {
	}
}
