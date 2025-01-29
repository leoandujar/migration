<?php

namespace App\Apis\Shared\DTO;

class ReportTemplateDto
{
	public function __construct(
		public ?string $id,
		public string $name,
		public int $format,
		public array $charts,
		public ?array $filters,
		public ?array $predefinedData,
		public ?array $categoryGroups,
		public ?string $template
	) {
	}
}
