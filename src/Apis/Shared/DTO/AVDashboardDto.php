<?php

namespace App\Apis\Shared\DTO;

class AVDashboardDto
{
	public function __construct(
		public string $id,
		public ?string $slug,
		public ?string $name,
		public ?string $type,
		public ?string $description,
		public ?string $category,
		public ?array $options = [],
	) {
	}
}
