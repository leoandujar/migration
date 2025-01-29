<?php

namespace App\Apis\Shared\DTO;

class GenericPersonDto
{
	public function __construct(
		public ?string $id,
		public ?string $firstName,
		public ?string $lastName,
		public ?string $email,
	) {
	}
}
