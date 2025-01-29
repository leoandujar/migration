<?php

namespace App\Apis\Shared\DTO;

class APFormTemplateDto
{
	/**
	 * APFormTemplateDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public string $name,
		public ?int $type,
		public ?string $content
	) {
	}
}
