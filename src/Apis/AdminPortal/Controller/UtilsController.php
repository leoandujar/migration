<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Http\Request\Utils\EnclosuresRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\AdminPortal\Handlers\UtilsHandler;
use App\Service\Notification\NotificationService;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifyTeamRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifyPmEmailRequest;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifyMailerEmailRequest;
use App\Apis\CustomerPortal\Http\Request\Utils\NotifySmsRequest;
use App\Apis\AdminPortal\Http\Request\Utils\MacroRequest;

#[Route(path: '/utils')]
class UtilsController extends AbstractController
{
	private LoggerService $loggerSrv;
	private UtilsHandler $utilsHandler;

	public function __construct(
		UtilsHandler $utilsHandler,
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
		$this->utilsHandler = $utilsHandler;
	}

	#[Route('/notify', name: 'ap_utils_notify', methods: ['POST'])]
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
				case NotificationService::NOTIFICATION_TYPE_SMS:
					$requestObj = new NotifySmsRequest($params);
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

	#[Route('/images/token', name: 'ap_utils_images_token', methods: ['GET'])]
	public function getImagesToken(): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->utilsHandler->processGetImageToken();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting image token.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/macro', name: 'ap_utils_macro', methods: ['POST'])]
	public function macro(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new MacroRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->utilsHandler->processMacro($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error processing macro.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/enclosures', name: 'ap_utils_enclosures', methods: ['POST'])]
	public function enclosures(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new EnclosuresRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->utilsHandler->processEnclosures($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error processing enclosures.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
