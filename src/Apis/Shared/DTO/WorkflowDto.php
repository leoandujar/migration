<?php

namespace App\Apis\Shared\DTO;

class WorkflowDto
{
	public function __construct(
		public string $id,
		public string $name,
		public ?string $description,
		public int $workflowType,
		public int $notificationType,
		public ?string $notificationTarget,
		public array $params,
		public ?bool $runAutomatically,
		public ?string $lastRunAt,
		public ?string $runPattern,
		public ?array $categoryGroups,
	) {
	}
}
