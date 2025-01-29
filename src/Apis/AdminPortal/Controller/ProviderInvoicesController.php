<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\ProviderInvoiceHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\ProviderInvoice\ProviderInvoiceListRequest;
use App\Apis\Shared\Http\Response\ApiResponse;

#[Route(path: '/provider-invoices')]
class ProviderInvoicesController extends AbstractController
{
	private ProviderInvoiceHandler $providerInvoiceHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		ProviderInvoiceHandler $providerInvoiceHandler,
		LoggerService $loggerSrv
	) {
		$this->providerInvoiceHandler = $providerInvoiceHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_provider_invoices_list', methods: ['GET'])]
	public function getInvoices(Request $request): ErrorResponse|ApiResponse
	{
		try {
			$requestObj = new ProviderInvoiceListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->providerInvoiceHandler->processGetInvoices($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving provider invoices list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
