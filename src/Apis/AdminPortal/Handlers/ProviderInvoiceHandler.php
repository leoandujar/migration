<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Model\Repository\ProviderInvoiceRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Error\ApiError;

class ProviderInvoiceHandler
{
	private ProviderInvoiceRepository $providerInvoiceRepository;

	public function __construct(
		ProviderInvoiceRepository $providerInvoiceRepository,
	) {
		$this->providerInvoiceRepository = $providerInvoiceRepository;
	}

	public function processGetInvoices(array $params): ApiResponse
	{
		if ($params['only_ids']) {
			$result = $this->providerInvoiceRepository->getSearchInvoicesIds($params);

			return new ApiResponse(data: $result);
		}

		if (empty($params['provider_id']) && empty($params['search'])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'provider_id');
		}

		$result = $this->providerInvoiceRepository->getSearchInvoices($params)->toArray();

		return new ApiResponse(data: $result);
	}
}
