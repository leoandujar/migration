<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Dashboard\ChartRequest;
use App\Apis\CustomerPortal\Http\Request\Dashboard\DashboardAddRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\DashboardHandler;
use App\Apis\CustomerPortal\Http\Request\Dashboard\WidgetRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/dashboard')]
class DashboardController extends AbstractController
{
	private LoggerService $loggerSrv;
	private DashboardHandler $dashboardHandler;

	public function __construct(
		DashboardHandler $dashboardHandler,
		LoggerService $loggerSrv
	) {
		$this->dashboardHandler = $dashboardHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('/permissions', name: 'cp_dashboard_permissions', methods: ['GET'])]
	public function getPermissions(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->dashboardHandler->processPermissions();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting permissions.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/widgets/{slug}', name: 'cp_dashboard_widget', methods: ['GET'])]
	public function getWidgets(Request $request, string $slug): ApiResponse|ErrorResponse
	{
		try {
			$params = $request->query->all();
			$requestObj = new WidgetRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->dashboardHandler->processWidgets(array_merge($requestObj->getParams(), ['slug' => $slug]));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting widget.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/graphs/{id}', name: 'cp_dashboard_graph', methods: ['GET'])]
	public function getGraphs(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$params = $request->query->all();
			$requestObj = new ChartRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->dashboardHandler->processGraphs(array_merge($requestObj->getParams(), ['graph_id' => $id]));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting graph.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_dashboard_list', methods: ['GET'])]
	public function list(): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->dashboardHandler->processList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting dashboard list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}


	#[Route('', name: 'cp_dashboard_create', methods: ['POST'])]
	public function add(Request $request): ApiResponse
	{
		try {
			$requestObj = new DashboardAddRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->dashboardHandler->processAdd($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding chart to dashboard.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}


	#[Route('/gallery', name: 'cp_dashboard_gallery', methods: ['GET'])]
	public function getGallery(): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->dashboardHandler->processGallery();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting dashboard gallery.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
