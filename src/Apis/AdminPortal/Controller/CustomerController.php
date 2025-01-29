<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Http\Request\ContactPerson\CustomerContactListRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\CustomerHandler;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Customer\CustomerListRequest;
use App\Apis\AdminPortal\Http\Request\Customer\AssignRoleRequest;

#[Route(path: '/customers')]
class CustomerController extends AbstractController
{
	private LoggerService $loggerSrv;

	private CustomerHandler $customerHandler;

	public function __construct(
		CustomerHandler $customerHandler,
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->customerHandler = $customerHandler;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_customer_list', methods: ['GET'])]
	public function getCustomers(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new CustomerListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerHandler->processGetCustomers($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting customer list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_customer_retrieve', methods: ['GET'])]
	public function getCustomer(string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerHandler->processGetCustomer($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting customer.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/members', name: 'ap_customer_contacts_list', methods: ['GET'])]
	public function getContacts(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new CustomerContactListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerHandler->processGetContacts(array_merge($requestObj->getParams(), ['id' => $id]));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting customer contacts list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/roles', name: 'ap_customer_role_update', methods: ['PUT'])]
	public function assignRole(Request $request, string $id): ApiResponse|ErrorResponse
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
			$response = $this->customerHandler->processAssingRole($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding role to customer.', $thr);
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
			$response = $this->customerHandler->processGetRoles(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error trying to get role list for customer. ID: $id", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
