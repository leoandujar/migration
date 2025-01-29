<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Repository\BlCallRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Util\UtilsService;

class OpiHandler
{
	private UtilsService $utilsSrv;
	private BlCallRepository $blCallRepository;

	public function __construct(
		UtilsService $utilsSrv,
		BlCallRepository $blCallRepository,
	) {
		$this->utilsSrv = $utilsSrv;
		$this->blCallRepository = $blCallRepository;
	}

	public function processGetCalls(array $params): ApiResponse
	{
		if (empty($params['customer_id'])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'customer_id');
		}

		if (isset($params['start_date']) && isset($params['end_date'])) {
			$startDate = new \DateTime($params['start_date']);
			$endDate = new \DateTime($params['end_date']);
			if ($startDate > $endDate) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'start_date');
			}
		}

		$calls = $this->blCallRepository->getCalls($params);
		$totalsResult = $this->blCallRepository->getCallsTotals($params);

		$totals = [
			'amount' => $this->utilsSrv->amountFormat($totalsResult['amount']),
			'duration' => $totalsResult['duration'],
		];

		$result = [];
		foreach ($calls as $call) {
			$result[] = Factory::blCallDtoInstance($call);
		}

		return new ApiResponse(data: [
			'calls' => $result,
			'totals' => $totals,
		]);
	}
}
