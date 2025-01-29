<?php

namespace App\Apis\Shared\DTO;

class APFormSubmissionDto
{
	/**
	 * APFormSubmissionDto constructor.
	 */
	public function __construct(
		public ?string $form,
		public ?array $approvers,
		public ?string $submittedBy,
		public ?string $approvedBy,
		public ?GenericPersonDto $owner,
		public ?array $collaborators,
		public ?string $id,
		public ?string $status,
		public ?string $submittedAt,
		public ?string $updatedAt,
		public ?array $submittedData
	) {
	}
}
