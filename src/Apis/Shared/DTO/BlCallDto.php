<?php

namespace App\Apis\Shared\DTO;

class BlCallDto
{
	public function __construct(
		public ?int $id,
		public ?string $date,
		public ?string $language,
		public ?string $requester,
		public ?int $duration,
		public ?float $amount,
	) {
	}
}
