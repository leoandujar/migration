<?php

namespace App\Apis\Shared\DTO;

class CustomerRuleDto
{
	public function __construct(
		public string $id,
		public string $name,
		public string $event,
		public string $type,
		public ?array $filters = [],
		public ?array $parameters = [],
		public ?CustomerDto $customer = null,
		public ?WorkflowDto $workflow = null
	) {
	}
}
