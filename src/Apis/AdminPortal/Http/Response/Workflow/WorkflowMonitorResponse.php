<?php

declare(strict_types=1);

namespace App\Apis\AdminPortal\Http\Response\Workflow;

use App\Apis\Shared\DTO\GenericPersonDto;
use App\Apis\Shared\DTO\WorkflowMonitorDto;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Constant\DateConstant;
use App\Model\Entity\AVWorkflowMonitor;

class WorkflowMonitorResponse extends ApiResponse
{
	protected bool $enablePagination = true;

	public function __construct(mixed $data = null)
	{
		parent::__construct();
		$this->updateData($data);
	}

	public function marshall(mixed $data = null): array
	{
		$result = [];
		foreach ($data['entities'] as $entity) {
			$result[] = $this->createDto($entity);
		}

		return $result;
	}

	private function createDto(AVWorkflowMonitor $entity): WorkflowMonitorDto
	{
		$createdBy = $entity->getCreatedBy();

		$createdByDto = new GenericPersonDto(
			$createdBy->getId(),
			$createdBy->getFirstName(),
			$createdBy->getLastName(),
			$createdBy->getEmail(),
		);

		return new WorkflowMonitorDto(
			$entity->getId(),
			$createdByDto,
			$entity->getWorkflow()->getName(),
			$entity->getStatus(),
			$entity->getWorkflow()->getType(),
			$entity->getOrderedAt()?->format(DateConstant::GLOBAL_FORMAT),
			$entity->getStartedAt()?->format(DateConstant::GLOBAL_FORMAT),
			$entity->getFinishedAt()?->format(DateConstant::GLOBAL_FORMAT),
			$entity->getDetails(),
		);
	}
}
