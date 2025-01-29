<?php

namespace App\Apis\Shared\DTO;

class AnalyticProjectDto
{
	/**
	 * AnalyticProjectDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public ?string $externalId,
		public ?string $name,
		public ?string $projectHumanId,
		public ?string $targetLanguageCode,
		public ?string $status,
		public ?string $processingStatus,
		public ?bool $ignored,
		public ?bool $lqaAllowed,
		public ?bool $lqaProcessed,
		public ?string $finishDate,
		public ?array $activity,
	) {
	}
}
