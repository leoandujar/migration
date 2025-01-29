<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Repository\CustomerInvoiceRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Error\ApiError;

class CustomerInvoiceHandler
{
	private CustomerInvoiceRepository $customerInvoiceRepository;

	public function __construct(
		CustomerInvoiceRepository $customerInvoiceRepository,
	) {
		$this->customerInvoiceRepository = $customerInvoiceRepository;
	}

	public function processGetInvoices(array $params): ApiResponse
	{
		if ($params['only_ids']) {
			$result = $this->customerInvoiceRepository->getSearchInvoicesIds($params);

			return new ApiResponse(data: $result);
		}

		if (empty($params['customer_id']) && empty($params['search'])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'customer_id');
		}

		$result = $this->customerInvoiceRepository->getSearchInvoices($params);
		$invoices = [];
		foreach ($result as $invoice) {
			$invoices[] = Factory::invoiceDtoInstance($invoice, false);
		}

		return new ApiResponse(data: $invoices);
	}
}
