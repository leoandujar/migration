<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\ReportChartHandler;
use App\Apis\AdminPortal\Http\Request\ReportChart\ReportChartCreateRequest;
use App\Apis\AdminPortal\Http\Request\ReportChart\ReportChartSearchRequest;
use App\Apis\AdminPortal\Http\Request\ReportChart\ReportChartUpdateRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/reports/charts')]
class ReportChartController extends AbstractController
{
	private ReportChartHandler $chartHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		ReportChartHandler $chartHandler,
		LoggerService $loggerSrv
	) {
		$this->chartHandler = $chartHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_chart_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ReportChartSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->chartHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting chart list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_chart_retrieve', methods: ['GET'])]
	public function retrieve(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->chartHandler->retrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving reportType.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_chart_create', methods: ['POST'])]
	public function create(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ReportChartCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->chartHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating chart.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_chart_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ReportChartUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->chartHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating chart.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_chart_delete', methods: ['DELETE'])]
	public function delete(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->chartHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting chart.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
