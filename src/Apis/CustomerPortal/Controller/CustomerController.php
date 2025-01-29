<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Account\SwitchCustomerRequest;
use App\Apis\CustomerPortal\Http\Request\Customer\UpdateCustomerRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\CustomerHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/customer')]
class CustomerController extends AbstractController
{
	private LoggerService $loggerSrv;
	private CustomerHandler $customerHandler;

	public function __construct(
		LoggerService $loggerSrv,
		CustomerHandler $customerHandler,
	) {
		$this->customerHandler = $customerHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_customer_retrieve', methods: ['GET'])]
	public function retrieve(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerHandler->processRetrieve();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving customer.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_customer_update', methods: ['PUT'])]
	public function update(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateCustomerRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->customerHandler->processUpdate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating customer.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
	
	#[Route('/switch', name: 'cp_customer_switch', methods: ['POST'])]
	public function switchCustomer(Request $request): ApiResponse
	{
		try {
			$requestObj = new SwitchCustomerRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['ip' => $request->getClientIp()]);
			$response = $this->customerHandler->processSwitch($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in switch customer.', $thr);
			$response = new ErrorResponse();
		}
		
		return $response;
	}
}
