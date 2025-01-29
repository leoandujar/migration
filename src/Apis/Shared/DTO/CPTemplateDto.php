<?php

namespace App\Apis\Shared\DTO;

class CPTemplateDto
{
	/**
	 * CPTemplateDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?int $type,
		public ?array $data,
		public ?bool $owner,
	) {
	}
}
