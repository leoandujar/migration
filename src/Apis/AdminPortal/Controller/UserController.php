<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Http\Request\Users\UserCustomerAssignRequest;
use App\Apis\AdminPortal\Http\Request\Users\UserOptionsListRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\AdminPortal\Handlers\UserHandler;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Http\Request\Users\UserCreateRequest;
use App\Apis\AdminPortal\Http\Request\Users\UserUpdateRequest;
use App\Apis\AdminPortal\Http\Request\Users\UserListRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Users\UserPasswordChangeRequest;

#[Route(path: '/users')]
class UserController extends AbstractController
{
	private LoggerService $loggerSrv;
	private UserHandler $userHandler;

	public function __construct(
		UserHandler $userHandler,
		LoggerService $loggerSrv
	) {
		$this->userHandler = $userHandler;
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_user_list', methods: ['GET'])]
	public function getList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UserListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->userHandler->processGetList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the user list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/options', name: 'ap_user_options', methods: ['GET'])]
	public function getOptions(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UserOptionsListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->userHandler->processGetOptions($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the user list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_user_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UserCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->userHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error user create action.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_user_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UserUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->userHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error user update action.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/change-password/{id}', name: 'ap_user_change_password', methods: ['PUT'])]
	public function changePassword(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UserPasswordChangeRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->userHandler->processChangePassword($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error changing user password.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_user_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->userHandler->processDelete(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/destroy', name: 'ap_user_destroy', methods: ['DELETE'])]
	public function destroy(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processDestroy($request);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error destroying user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/customers', name: 'ap_user_customers_update', methods: ['PUT'])]
	public function assignLoginCustomer(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UserCustomerAssignRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->userHandler->processAssignCpLoginCustomer($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error assigning customer to internal user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
