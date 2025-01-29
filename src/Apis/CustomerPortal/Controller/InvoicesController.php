<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Invoice\InvoiceCheckoutRequest;
use App\Apis\CustomerPortal\Http\Request\Invoice\InvoiceCheckoutStatusRequest;
use App\Apis\CustomerPortal\Http\Request\Invoice\InvoiceExportRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\InvoicesHandler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Invoice\GetInvoicesRequest;

#[Route(path: '/invoices')]
class InvoicesController extends AbstractController
{
	private LoggerService $loggerSrv;
	private InvoicesHandler $invoicesHandler;

	public function __construct(
		InvoicesHandler $projectHandler,
		LoggerService $loggerSrv
	) {
		$this->invoicesHandler = $projectHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('/{id}', name: 'cp_invoice', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getInvoice(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processGetInvoice($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving invoice.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/files', name: 'cp_invoice_files', methods: ['POST'])]
	public function export(Request $request): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new InvoiceExportRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processGeneratePdf($requestObj->getParams());
			$response->deleteFileAfterSend();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error generating pdf for invoice.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/checkout', name: 'cp_invoice_checkout', methods: ['Post'])]
	public function checkout(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new InvoiceCheckoutRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processCheckout($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error generating payment checkout for invoice.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/checkout/status', name: 'cp_invoice_checkout_status', methods: ['POST'])]
	public function checkoutStatus(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new InvoiceCheckoutStatusRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processCheckoutStatus($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error checking payment checkout status.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/payment', name: 'cp_invoice_payment', requirements: ['id' => '\d+'], methods: ['Post'])]
	public function payment(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processPayment($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error executing pre payment for invoice $id.", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/payment/{success}', name: 'cp_invoice_payment_result', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function paymentResult(int $id, bool $success = false): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processPaymentResult(
				[
					'id' => $id,
					'success' => $success,
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error processing payment result invoice $id.", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_invoice_list', methods: ['GET'])]
	public function getInvoices(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new GetInvoicesRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processGetInvoices($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting invoices list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/export', name: 'cp_invoice_export', methods: ['POST'])]
	public function exportInvoices(Request $request): BinaryFileResponse|ApiResponse
	{
		try {
			$requestObj = new GetInvoicesRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->invoicesHandler->processExportInvoices($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error exporting invoice.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
