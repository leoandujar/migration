<?php

namespace App\Apis\Shared\DTO;

class ContactPersonDto
{
	public function __construct(
		public ?string $id,
		public ?bool $active,
		public ?ContactPersonBasictDto $contact,
		public ?string $personDepartment,
		public ?string $picture,
		public ?bool $twoFactorEnabled,
		public ?array $roles,
		public ?array $abilities,
		public ?string $office,
		public ?array $preferences,
		public ?string $username,
		public ?string $scope,
		public ?string $status,
		public ?string $lastLogin,
		public ?string $lastFailedLogin,
		public ?array $onboarding,
		public ?\DateTimeInterface $passwordUpdatedAt,
	) {
	}
}
