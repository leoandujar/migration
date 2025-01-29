<?php

namespace App\Apis\Shared\DTO;

class SimpleWorkFlowDto
{
	public function __construct(
		public string $id,
		public string $name,
		public ?string $description,
		public int $workflowType
	) {
	}
}
