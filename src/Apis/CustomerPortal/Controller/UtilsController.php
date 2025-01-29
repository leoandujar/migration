<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\General\DefaultEstimateRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Service\Notification\NotificationService;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\UtilsHandler;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifyTeamRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifyMailerEmailRequest;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifyPmEmailRequest;
use App\Apis\CustomerPortal\Http\Request\Utils\GlobalSearchRequest;

#[Route(path: '/utils')]
class UtilsController extends AbstractController
{
	private LoggerService $loggerSrv;
	private UtilsHandler $utilsHandler;

	public function __construct(
		UtilsHandler $utilsHandler,
		LoggerService $loggerSrv
	) {
		$this->utilsHandler = $utilsHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('/notify', name: 'cp_notify', methods: ['POST'])]
	public function notify(Request $request): ApiResponse|ErrorResponse
	{
		try {
			
			$type = $request->getPayload()->get('type');
			$params = $request->getPayload()->all();
			switch ($type) {
				case NotificationService::NOTIFICATION_TYPE_TEAM:
					$requestObj = new NotifyTeamRequest($params);
					break;
				case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
					$requestObj = new NotifyPmEmailRequest($params);
					break;
				case NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL:
					$requestObj = new NotifyMailerEmailRequest($params);
					break;
			}

			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->utilsHandler->processNotify($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error sending notify.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/search', name: 'cp_search', methods: ['GET'])]
	public function globalSearch(Request $request): ApiResponse|ErrorResponse
	{
		try {
			
			$requestObj = new GlobalSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->utilsHandler->processGlobalSearch($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving global search data.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

    #[Route('/estimate', name: 'cp_utils_estimate', methods: ['GET'])]
    public function getEstimate(Request $request): ApiResponse
    {
        try {
            $requestObj = new DefaultEstimateRequest($request->query->all());
            if (!$requestObj->isValid()) {
                return $requestObj->getError();
            }
            $response = $this->utilsHandler->processGetEstimate($requestObj->getParams());
        } catch (\Throwable $thr) {
            $this->loggerSrv->addError('Error getting estimate.', $thr);
            $response = new ErrorResponse();
        }

        return $response;
    }
}
