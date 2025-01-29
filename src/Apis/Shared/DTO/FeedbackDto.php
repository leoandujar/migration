<?php

namespace App\Apis\Shared\DTO;

class FeedbackDto
{
	/**
	 * FeedbackDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public ?string $creationDate,
		public ?string $description,
		public ?string $status
	) {
	}
}
