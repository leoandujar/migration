<?php

namespace App\Apis\Shared\DTO;

class ProjectDtoV2
{
	public function __construct(
		public ?string $id,
		public ?string $idNumber,
		public ?string $refNumber,
		public ?string $name,
		public ?string $service,
		public ?string $workflow,
		public ?string $specialization,
		public ?string $startDate,
		public ?string $deliveryDate,
		public ?string $office,
		public ?string $customerNotes,
		public ?string $status,
		public ?string $projectManager,
		public ?bool $isProject,
		public ?bool $projectConfirmationAvailable,
	) {
	}
}
