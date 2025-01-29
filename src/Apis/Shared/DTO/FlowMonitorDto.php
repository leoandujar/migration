<?php

namespace App\Apis\Shared\DTO;

class FlowMonitorDto
{
	public function __construct(
		public ?string $id,
		public ?string $requestedBy,
		public ?string $flowName,
		public ?int $status,
		public ?string $requestedAt,
		public ?string $startedAt,
		public ?string $finishedAt,
		public ?array $details,
		public ?array $result,
		public ?array $auxiliaryData,
	) {
	}
}
