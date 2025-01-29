<?php

namespace App\Apis\Shared\DTO;

class CustomerDto
{
	/**
	 * CustomerDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?string $parentName,
		public ?CustomerAddressDto $address,
		public ?array $responsiblePersons,
		public ?array $roles,
		public ?array $categoryGroups,
	) {
	}
}
