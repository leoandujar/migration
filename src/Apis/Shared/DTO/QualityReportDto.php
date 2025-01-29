<?php

namespace App\Apis\Shared\DTO;

class QualityReportDto
{
	/**
	 * QualityReportDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public ?string $activity,
		public ?string $prooferName,
		public ?int $pageCount,
		public ?string $format,
		public ?float $score,
		public ?string $status,
		public ?int $issueCount,
		public ?array $qualityIssues,
		public ?string $type,
		public int $minorMultiplier,
		public int $majorMultiplier,
		public int $criticalMultiplier
	) {
	}
}
