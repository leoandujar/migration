<?php

namespace App\Apis\Shared\DTO;

class AVChartDto
{
	public function __construct(
		public ?string $id,
		public string $slug,
		public ?string $name,
		public ?string $description,
		public string $category,
		public ?int $type,
		public ?string $returnY,
		public bool $active,
		public ?int $size,
		public ?array $options,
		public ?int $reportType,
	) {
	}
}
