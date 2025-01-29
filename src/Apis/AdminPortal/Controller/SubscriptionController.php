<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\SubscriptionHandler;
use App\Apis\AdminPortal\Http\Request\Subscription\CreateSubscriptionRequest;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Request\ApiRequest;

#[Route(path: '/subscriptions')]
class SubscriptionController extends AbstractController
{
	private LoggerService $loggerSrv;
	private SubscriptionHandler $subscriptionHandler;

	public function __construct(
		SubscriptionHandler $subscriptionHandler,
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->subscriptionHandler = $subscriptionHandler;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route(path: '/xtrf', name: 'ap_subscription_xtrf_list', methods: ['GET'])]
	public function getList(): ApiResponse|ErrorResponse
	{
		try {
			do {
				$requestObj = new ApiRequest();
				if (!$requestObj->isValid()) {
					$response = $requestObj->getError();
					break;
				}
				$response = $this->subscriptionHandler->processGetList();
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the xtrf subscription list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route(path: '/xtrf', name: 'ap_subscription_xtrf_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			do {
				$requestObj = new CreateSubscriptionRequest($request->getPayload()->all());
				if (!$requestObj->isValid()) {
					$response = $requestObj->getError();
					break;
				}
				$response = $this->subscriptionHandler->processCreate($requestObj->getParams());
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating xtrf subscription.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route(path: '/xtrf/{id}', name: 'ap_subscription_xtrf_retrieve', methods: ['DELETE'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			do {
				$requestObj = new ApiRequest();
				if (!$requestObj->isValid()) {
					$response = $requestObj->getError();
					break;
				}
				$response = $this->subscriptionHandler->processDelete($id);
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting xtrf subscription.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
