<?php

namespace App\Apis\Shared\DTO;

class AvCustomerRuleDto
{
	public function __construct(
		public string $id,
		public string $name,
		public string $event,
		public ?string $type,
		public ?array $filters = [],
		public ?array $parameters = [],
		public ?SimpleObjDto $customer = null,
		public ?SimpleWorkFlowDto $workflow = null
	) {
	}
}
