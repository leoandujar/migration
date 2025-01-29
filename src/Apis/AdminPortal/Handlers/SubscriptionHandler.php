<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Connector\Xtrf\XtrfConnector;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\XtrfWebhookService;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionHandler
{

	private XtrfConnector $xtrfConn;

	public function __construct(
		XtrfConnector $xtrfConn,
	) {
		$this->xtrfConn = $xtrfConn;
	}

	public function processGetList(): ApiResponse
	{
		$response = $this->xtrfConn->getSubscriptions();
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		return new ApiResponse(
			data: $response->getRaw()
		);
	}

	public function processCreate(array $params): ApiResponse
	{
		$event = $params['event'];

		if (!in_array($event, [XtrfWebhookService::EVENT_PROJECT_CREATED, XtrfWebhookService::EVENT_CUSTOMER_UPDATED])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'event');
		}

		$response = $this->xtrfConn->createSubscription($params);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		return new ApiResponse(code: Response::HTTP_CREATED);
	}

	public function processDelete(string $id): ApiResponse
	{
		$response = $this->xtrfConn->deleteSubscription($id);

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
