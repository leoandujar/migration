<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Http\Request\ContactPerson\AssignRoleRequest;
use App\Apis\CustomerPortal\Http\Request\Member\UpdateScopeMemberRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\ContactPersonHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\ContactPerson\ContactPersonListRequest;
use App\Apis\AdminPortal\Http\Request\ContactPerson\CreateOneTimeLoginRequest;

#[Route(path: 'members')]
class ContactPersonController extends AbstractController
{
	private ContactPersonHandler $contactPersonHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		ContactPersonHandler $contactPersonHandler,
		LoggerService $loggerSrv
	) {
		$this->contactPersonHandler = $contactPersonHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_member_list', methods: ['GET'])]
	public function getContactPersons(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ContactPersonListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->contactPersonHandler->processGetContactPersons($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in contact person list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/one-time-login', name: 'ap_member_onetime_token', methods: ['POST'])]
	public function generateOneTimeToken(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$params = array_merge(
				$request->getPayload()->all(),
				[
					'contact_person_id' => $id,
					'ip' => $request->getClientIp(),
				]
			);
			$requestObj = new CreateOneTimeLoginRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->contactPersonHandler->processGenerateOneTimeToken($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error generating Client Portal user one time token.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/reset-password', name: 'ap_contact_person_reset_password', methods: ['GET'])]
	public function resetPassword(string $id): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->contactPersonHandler->processResetPassword(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error trying to reset password for Customer Portal User. ID: $id", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/roles', name: 'ap_contact_person_role_update', methods: ['PUT'])]
	public function assingRole(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$params = array_merge(
				$request->getPayload()->all(),
				['id' => $id]
			);
			$requestObj = new AssignRoleRequest($params);

			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->contactPersonHandler->processAssingRole($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding role to user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/roles', name: 'ap_contact_person_role_list', methods: ['GET'])]
	public function getRoles(string $id): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->contactPersonHandler->processGetRoles(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error trying to get role list for user. ID: $id", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/scope', name: 'ap_contact_person_scope_update', methods: ['PUT'])]
	public function updateScope(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateScopeMemberRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->contactPersonHandler->processUpdateScope($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding role to user.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
