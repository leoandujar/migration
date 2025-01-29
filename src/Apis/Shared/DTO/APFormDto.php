<?php

namespace App\Apis\Shared\DTO;

class APFormDto
{
	public function __construct(
		public ?string $id,
		public ?GenericPersonDto $createdBy,
		public ?array $approvers,
		public ?APFormTemplateDto $template,
		public ?int $category,
		public ?string $name,
		public ?string $createdAt,
		public ?string $pmkTemplateId
	) {
	}
}
