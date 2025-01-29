<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Handlers\AccountHandler;
use App\Apis\CustomerPortal\Http\Request\Account\ChangePasswordRequest;
use App\Apis\CustomerPortal\Http\Request\Account\UpdateAccountRequest;
use App\Apis\CustomerPortal\Http\Request\Account\UpdatePictureProfileRequest;
use App\Apis\CustomerPortal\Http\Request\Account\UpdatePreferencesRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/account')]
class AccountController extends AbstractController
{
	private LoggerService $loggerSrv;
	private AccountHandler $accountHandler;

	public function __construct(
		AccountHandler $accountHandler,
		LoggerService $loggerSrv
	) {
		$this->accountHandler = $accountHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_account_retrieve', methods: ['GET'])]
	public function getContactPerson(): ApiResponse|ErrorResponse
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

	#[Route('', name: 'cp_account_update', methods: ['PUT'])]
	public function update(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateAccountRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processUpdate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating contact person info.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/2fa', name: 'cp_account_two_factor_update', methods: ['PUT'])]
	public function enableTwoFactor(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processUpdateTwoFactor();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error activating two factor.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/picture', name: 'cp_account_picture_update', methods: ['POST'])]
	public function updatePicture(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdatePictureProfileRequest($request->files->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processUpdatePicture($request->files->all());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating account profile image.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/picture', name: 'cp_account_picture_delete', methods: ['DELETE'])]
	public function deletePicture(Request $request): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processDeletePicture();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting profile picture.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/password', name: 'cp_account_password_update', methods: ['PUT'])]
	public function changePassword(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ChangePasswordRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processChangePassword($requestObj->getParams());
		} catch (\Throwable|GuzzleException $thr) {
			$this->loggerSrv->addError('Error updating contact person info.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/preferences', name: 'cp_account_preferences_update', methods: ['PUT'])]
	public function updatePreferences(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdatePreferencesRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->accountHandler->processUpdatePreferences($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating preferences.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
