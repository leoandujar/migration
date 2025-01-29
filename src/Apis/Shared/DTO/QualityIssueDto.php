<?php

namespace App\Apis\Shared\DTO;

class QualityIssueDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?int $minor,
		public ?int $major,
		public ?int $critical,
		public ?string $comment
	) {
	}
}
