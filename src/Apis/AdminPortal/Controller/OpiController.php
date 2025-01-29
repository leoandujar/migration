<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\OpiHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Opi\CallListRequest;
use App\Apis\Shared\Http\Response\ApiResponse;

#[Route(path: '/opi')]
class OpiController extends AbstractController
{
	private OpiHandler $OpiHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		OpiHandler $OpiHandler,
		LoggerService $loggerSrv
	) {
		$this->OpiHandler = $OpiHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/calls', name: 'ap_opi_call_list', methods: ['GET'])]
	public function callList(Request $request): ErrorResponse|ApiResponse
	{
		try {
			$requestObj = new CallListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->OpiHandler->processGetCalls($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving call list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
