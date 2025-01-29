<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\ActivityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Activity\ActivityListRequest;

#[Route(path: '/activities')]
class ActivityController extends AbstractController
{
	private ActivityHandler $activityHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		ActivityHandler $activityHandler,
		LoggerService $loggerSrv
	) {
		$this->activityHandler = $activityHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_activity_list', methods: ['GET'])]
	public function activityList(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ActivityListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->activityHandler->processActivityList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving activity list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
