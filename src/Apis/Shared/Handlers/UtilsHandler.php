<?php

namespace App\Apis\Shared\Handlers;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Connector\XtrfMacro\MacroConnector;

class UtilsHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private MacroConnector $macroConn;
	private LoggerService $loggerSrv;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MacroConnector $macroConn,
	) {
		$this->em = $em;
		$this->macroConn = $macroConn;
		$this->loggerSrv = $loggerSrv;
	}

	public function processMacro(array $dataRequest): ApiResponse
	{
		$async = $dataRequest['async'] ?? false;
		$macroResponse = $this->macroConn->runMacro(
			$dataRequest['macro_id'],
			$dataRequest['ids'],
			$dataRequest['params'] ?? [],
			$async,
		);

		if (!$macroResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_ERROR,
				ApiError::$descriptions[ApiError::CODE_MACRO_RUN_ERROR]
			);
		}

		if (!$async) {
			if ($macroResponse->url) {
				$macroResult = file_get_contents($macroResponse->url);
				$jsonObject = json_decode($macroResult);

				return new ApiResponse(data: $jsonObject);
			}

			return new ApiResponse(data: $macroResponse);
		}

		$macroStatus = $this->macroConn->checkStatusTilCompleted($macroResponse->actionId);

		if (MacroConnector::STATUS_PENDING === $macroStatus) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_STILL_PENDING,
				ApiError::$descriptions[ApiError::CODE_MACRO_RUN_STILL_PENDING]
			);
		}

		$macroFile = $this->macroConn->getResult($macroResponse->actionId);

		return new ApiResponse(data: $macroFile);
	}
}
