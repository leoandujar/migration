<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\AccountHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Account\AccountUpdateRequest;
use App\Apis\AdminPortal\Http\Request\Account\AccountPasswordChangeRequest;

#[Route(path: '/account')]
class AccountController extends AbstractController
{
	private AccountHandler $accountHandler;

	private LoggerService $loggerSrv;

	public function __construct(
		AccountHandler $accountHandler,
		LoggerService $loggerSrv
	) {
		$this->accountHandler = $accountHandler;
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_account_update', methods: ['PUT'])]
	public function update(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AccountUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processUpdate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating account.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/password', name: 'ap_account_password_update', methods: ['PUT'])]
	public function changePassword(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AccountPasswordChangeRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processChangePassword($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating password.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/permissions', name: 'ap_account_permissions', methods: ['GET'])]
	public function getPermissions(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processGetPermissions();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting account permissions.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_account_retrieve', methods: ['GET'])]
	public function retrieve(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processRetrieve();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving account.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
