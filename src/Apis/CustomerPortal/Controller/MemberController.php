<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Handlers\MemberHandler;
use App\Apis\CustomerPortal\Http\Request\Member\CreateMemberRequest;
use App\Apis\CustomerPortal\Http\Request\Member\UpdateScopeMemberRequest;
use App\Apis\CustomerPortal\Http\Request\Member\UpdateMemberRequest;
use App\Apis\CustomerPortal\Http\Request\Member\UpdateMemberStatusRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/members')]
class MemberController extends AbstractController
{
	private LoggerService $loggerSrv;
	private MemberHandler $memberHandler;

	public function __construct(
		MemberHandler $memberHandler,
		LoggerService $loggerSrv
	) {
		$this->memberHandler = $memberHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_member_list', methods: ['GET'])]
	public function list(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->memberHandler->processList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting member list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_member_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CreateMemberRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->memberHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating member.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_member_retrieve', methods: ['GET'])]
	public function retrieve(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->memberHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving member.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_member_update', methods: ['PUT'])]
	public function update(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateMemberRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->memberHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating member.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/status', name: 'cp_member_status_update', methods: ['PUT'])]
	public function updateStatus(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateMemberStatusRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->memberHandler->processUpdateStatus($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating member status', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_member_delete', methods: ['DELETE'])]
	public function delete(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->memberHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting member.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/scope', name: 'cp_member_update_scope', methods: ['PUT'])]
	public function updateScope(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateScopeMemberRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->memberHandler->processUpdateScope($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating scope member.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
