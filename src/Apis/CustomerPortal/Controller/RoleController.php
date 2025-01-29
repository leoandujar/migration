<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\RoleHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Role\AssignRoleToUserRequest;

#[Route(path: '/roles')]
class RoleController extends AbstractController
{
	private LoggerService $loggerSrv;
	private RoleHandler $roleHandler;

	public function __construct(
		RoleHandler $roleHandler,
		LoggerService $loggerSrv
	) {
		$this->roleHandler = $roleHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'cp_role_list', methods: ['GET'])]
	public function getList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->roleHandler->processGetList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the role list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/assign-to-user', name: 'cp_role_sibling_update', methods: ['PUT'])]
	public function assingRoleToUser(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AssignRoleToUserRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->roleHandler->processAssingRoleToUser($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the role list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
