<?php

namespace App\Apis\Shared\DTO;

class InternalUserDto
{
	public function __construct(
		public ?string $id,
		public ?string $username,
		public ?string $firstName,
		public ?string $lastName,
		public ?string $email,
		public ?string $mobile,
		public mixed $status,
		public ?bool $allCustomersAccess,
		public ?array $cpLoginCustomers = [],
		public mixed $type = null,
		public ?array $roles = [],
		public ?string $position = null,
		public ?string $department = null,
		public ?array $tags = [],
		public ?array $categoryGroups = [],
		public ?array $abilities = [],
		public ?string $xtrfId = null,
	) {
	}
}
