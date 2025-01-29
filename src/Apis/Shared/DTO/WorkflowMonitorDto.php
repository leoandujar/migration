<?php

namespace App\Apis\Shared\DTO;

class WorkflowMonitorDto
{
	public function __construct(
		public string $id,
		public ?GenericPersonDto $createdBy,
		public string $workflowName,
		public int $status,
		public int $type,
		public string $triggeredAt,
		public ?string $startedAt,
		public ?string $finishedAt,
		public ?array $details
	) {
	}
}
