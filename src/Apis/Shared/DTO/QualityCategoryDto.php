<?php

namespace App\Apis\Shared\DTO;

class QualityCategoryDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?int $weight,
		public ?bool $isLeaf,
		public ?bool $isOther,
		public ?string $path,
		public ?string $pathDepth,
		public ?string $parentName,
		public ?string $parentCategory,
	) {
	}
}
