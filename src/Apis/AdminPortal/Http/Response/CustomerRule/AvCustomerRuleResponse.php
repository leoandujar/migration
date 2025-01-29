<?php

namespace App\Apis\AdminPortal\Http\Response\CustomerRule;

use App\Apis\Shared\DTO\AvCustomerRuleDto;
use App\Apis\Shared\DTO\SimpleObjDto;
use App\Apis\Shared\DTO\SimpleWorkFlowDto;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Model\Entity\AVCustomerRule;

class AvCustomerRuleResponse extends ApiResponse
{
	public function __construct(mixed $data = null)
	{
		parent::__construct();

		$this->updateData($data);
	}

	public function marshall(mixed $data = null): array
	{
		$result = [];
		/** @var AVCustomerRule $entity */
		foreach ($data['entities'] as $entity) {
			$result[] = $this->createDto($entity);
		}

		return $result;
	}

	private function createDto($entity): AvCustomerRuleDto
	{
		$customer = $entity->getCustomer();
		$workFlow = $entity->getWorkflow();

		$customerDto = new SimpleObjDto(
			$customer->getId(),
			$customer->getName()
		);

		$workflowDto = null;
		if ($workFlow) {
			$workflowDto = new SimpleWorkFlowDto(
				$workFlow->getId(),
				$workFlow->getName(),
				$workFlow->getDescription(),
				$workFlow->getType()
			);
		}

		return new AvCustomerRuleDto(
			$entity->getId(),
			$entity->getName(),
			$entity->getEvent(),
			$entity->getType(),
			$entity->getFilters(),
			$entity->getParameters(),
			$customerDto,
			$workflowDto
		);
	}
}
