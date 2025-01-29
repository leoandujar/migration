<?php

namespace App\Apis\Shared\DTO;

class ReportTypeDto
{
	public function __construct(
		public ?string $id,
		public string $name,
		public ?string $code,
		public ?string $description,
		public ?string $functionName,
		public ?ReportTypeDto $parent = null,
		public ?array $children = null
	) {
	}
}
