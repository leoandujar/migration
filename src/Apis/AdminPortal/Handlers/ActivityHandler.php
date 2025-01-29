<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Model\Repository\ActivityRepository;
use App\Apis\Shared\Http\Response\ApiResponse;

class ActivityHandler
{
	private ActivityRepository $activityRepository;

	public function __construct(
		ActivityRepository $activityRepository
	) {
		$this->activityRepository = $activityRepository;
	}

	public function processActivityList(array $params): ApiResponse
	{
		$partialName = $params['name'];
		$limit       = $params['limit'];
		$type	       = $params['type'] ?? null;
		$result      = $this->activityRepository->getActivities($partialName, $limit, $type);

		return new ApiResponse(data: $result);
	}
}
