<?php

namespace App\Apis\Shared\DTO;

class ContactPersonBasictDto
{
	public function __construct(
		public ?string $name,
		public ?string $lastName,
		public ?string $personPosition,
		public ?string $email,
		public ?string $phone,
		public ?string $addressPhone2,
		public ?string $addressPhone3,
		public ?string $mobilePhone,
		public ?string $fax
	) {
	}
}
