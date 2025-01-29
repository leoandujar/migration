<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\SettingHandler;
use App\Apis\AdminPortal\Http\Request\Settings\SettingRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Settings\UpdateSettingRequest;

#[Route(path: '/settings')]
class SettingController extends AbstractController
{
	private LoggerService $loggerSrv;
	private SettingHandler $settingHandler;

	public function __construct(
		SettingHandler $settingHandler,
		LoggerService $loggerSrv,
	) {
		$this->settingHandler = $settingHandler;

		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/schema', name: 'ap_customer_settings_schema', methods: ['GET'])]
	public function schema(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->settingHandler->processSchema($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the settings schema.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{customerId}', name: 'ap_customer_settings', methods: ['GET'])]
	public function getByCustomer(string $customerId): ApiResponse|ErrorResponse
	{
		try {
			$params['customer_id'] = $customerId;
			$requestObj = new SettingRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->settingHandler->processGetByCustomer($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting settings.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{customerId}', name: 'ap_customer_settings_update', methods: ['PUT'])]
	public function updateByCustomer(Request $request, string $customerId): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateSettingRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = $requestObj->getParams();
			$params['customer_id'] = $customerId;
			$response = $this->settingHandler->processUpdateByCustomer($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating settings.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/generate/{customerId}', name: 'ap_customer_settings_create', methods: ['POST'])]
	public function generate(string $customerId): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new SettingRequest(['customer_id' => $customerId]);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->settingHandler->processGenerateEmptySettings($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error generating empty settings.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
