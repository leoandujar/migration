<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\AdminPortal\Handlers\ActionHandler;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Security\RoleActionCreateRequest;
use App\Apis\AdminPortal\Http\Request\Security\RoleActionUpdateRequest;

#[Route(path: '/actions')]
class ActionController extends AbstractController
{
	private ActionHandler $actionHandler;

	private LoggerService $loggerSrv;

	public function __construct(
		ActionHandler $actionHandler,
		LoggerService $loggerSrv
	) {
		$this->actionHandler = $actionHandler;
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_action_list', methods: ['GET'])]
	public function getList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$target = $request->query->get('target') ?? null;
			$response = $this->actionHandler->processGetList($target);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the action list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_action_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new RoleActionCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->actionHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating action.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_action_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new RoleActionUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->actionHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating action.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_action_delete', methods: ['DELETE'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->actionHandler->processDelete(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting action.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
