<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\CustomerInvoiceHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\CustomerInvoice\CustomerInvoiceListRequest;
use App\Apis\Shared\Http\Response\ApiResponse;

#[Route(path: '/customer-invoices')]
class CustomerInvoicesController extends AbstractController
{
	private CustomerInvoiceHandler $customerInvoiceHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		CustomerInvoiceHandler $customerInvoiceHandler,
		LoggerService $loggerSrv
	) {
		$this->customerInvoiceHandler = $customerInvoiceHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_customer_invoices_list', methods: ['GET'])]
	public function getInvoices(Request $request): ErrorResponse|ApiResponse
	{
		try {
			$requestObj = new CustomerInvoiceListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->customerInvoiceHandler->processGetInvoices($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving customer invoices list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
