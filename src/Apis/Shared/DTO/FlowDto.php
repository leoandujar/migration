<?php

namespace App\Apis\Shared\DTO;

class FlowDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?string $description,
		public ?string $createdAt,
		public ?string $updatedAt,
		public ?string $deletedAt,
		public ?bool $runAutomatically,
		public ?string $lastRunAt,
		public ?string $runPattern,
		public ?array $params,
		public ?array $actions,
		public ?array $categoryGroup,
		public ?array $inputsOnStart,
	) {
	}
}
