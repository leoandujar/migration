<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\PermissionsHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Permissions\PermissionByUserRequest;
use App\Apis\AdminPortal\Http\Request\Permissions\PermissionUpdateRequest;

#[Route(path: '/permissions')]
class PermissionController extends AbstractController
{
	private LoggerService $loggerSrv;

	private PermissionsHandler $permissionHandler;

	public function __construct(
		PermissionsHandler $permissionHandler,
		LoggerService $loggerSrv
	) {
		$this->permissionHandler = $permissionHandler;
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/role/{id}', name: 'ap_permission_list_by_role', methods: ['GET'])]
	public function listByRole(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params['id'] = $id;
			$response = $this->permissionHandler->processListByRole($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting permissions by role.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/role/{id}', name: 'ap_permission_update_by_role', methods: ['PUT'])]
	public function updateByRole(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new PermissionUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->permissionHandler->processUpdateByRole($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating permissions by role.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/user/{type}/{id}', name: 'ap_permission_list_by_user', methods: ['GET'])]
	public function listByUser(string $id, string $type): ApiResponse|ErrorResponse
	{
		try {
			$params['type'] = $type;
			$requestObj = new PermissionByUserRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params['id'] = $id;
			$response = $this->permissionHandler->processListByUser($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting permissions by user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/user/{type}/{id}', name: 'ap_permission_update_by_user', methods: ['PUT'])]
	public function updateByUser(string $id, string $type, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$params['type'] = $type;
			$requestObj = new PermissionByUserRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$requestObj = new PermissionUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$params = array_merge($requestObj->getParams(), ['id' => $id, 'type' => $type]);
			$response = $this->permissionHandler->processUpdateByUser($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating permissions by user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
