<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\ResourceHandler;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/resources')]
class ResourceController extends AbstractController
{
	private LoggerService $loggerSrv;
	private ResourceHandler $resourceHandler;

	public function __construct(
		ResourceHandler $resourceHandler,
		LoggerService $loggerSrv
	) {
		$this->resourceHandler = $resourceHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/languages', name: 'ap_resources_languages_list', methods: ['GET'])]
	public function getLanguageList(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourceHandler->processGetLanguages();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the languages list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/wf-types', name: 'ap_resources_wf_type_list', methods: ['GET'])]
	public function getWfTypesList(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourceHandler->processGetWfTypeList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the workflow type list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/wf-notification-type', name: 'ap_resources_wf_notification_type_list', methods: ['GET'])]
	public function getWfWfNotificationTypeList(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourceHandler->processGetWfNotificationTypeList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the workflow notification type list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/wf-disk', name: 'ap_resources_wf_disk_list', methods: ['GET'])]
	public function getWfWfDiskList(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourceHandler->processGetWfDiskList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the workflow disk list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/xtrf-subscription-types', name: 'ap_xtrf_subscription_type_list', methods: ['GET'])]
	public function getXtrfSubscriptionTypeList(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourceHandler->processGetXtrfSubscriptionTypeList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the xtrf subscriptions type list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/categories', name: 'ap_resources_category_list', methods: ['GET'])]
	public function getCategoryList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourceHandler->processGetCategoryList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the category list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
