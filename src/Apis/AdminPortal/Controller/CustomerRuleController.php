<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\CustomerRuleHandler;
use App\Apis\AdminPortal\Http\Request\CustomerRule\CustomerRuleCreateRequest;
use App\Apis\AdminPortal\Http\Request\CustomerRule\CustomerRuleListRequest;
use App\Apis\AdminPortal\Http\Request\CustomerRule\CustomerRuleUpdateRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ApiResponse;

#[Route(path: '/customer-rule')]
class CustomerRuleController extends AbstractController
{
	private LoggerService $loggerSrv;
	private CustomerRuleHandler $customerRuleHandler;

	public function __construct(LoggerService $loggerSrv, CustomerRuleHandler $customerRuleHandler)
	{
		$this->customerRuleHandler = $customerRuleHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'av_customer_rule_list', methods: ['GET'])]
	public function getCustomerRulesList(Request $request): ApiResponse
	{
		try {
			$requestObj = new CustomerRuleListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerRuleHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving customer rules list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'av_customer_rule_get', methods: ['GET'])]
	public function retrieve(string $id): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerRuleHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving customer rule.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'av_customer_rule_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse
	{
		try {
			$requestObj = new CustomerRuleCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerRuleHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating customer rule.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_customer_rule_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse
	{
		try {
			$requestObj = new CustomerRuleUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->customerRuleHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating customer rule.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_customer_rule_delete', methods: ['DELETE'])]
	public function delete(string $id): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerRuleHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting customer rule.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
