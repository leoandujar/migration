<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\ReportTypeHandler;
use App\Apis\AdminPortal\Http\Request\ReportType\ReportTypeCreateRequest;
use App\Apis\AdminPortal\Http\Request\ReportType\ReportTypeSearchRequest;
use App\Apis\AdminPortal\Http\Request\ReportType\ReportTypeUpdateRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/reports/types')]
class ReportTypeController extends AbstractController
{
	private ReportTypeHandler $reportTypeHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		ReportTypeHandler $reportTypeHandler,
		LoggerService $loggerSrv
	) {
		$this->reportTypeHandler = $reportTypeHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_report_type_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ReportTypeSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->reportTypeHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting reportType list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_report_type_retrieve', methods: ['GET'])]
	public function retrieve(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->reportTypeHandler->retrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving reportType.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_report_type_create', methods: ['POST'])]
	public function create(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ReportTypeCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->reportTypeHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating reportType.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_report_type_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ReportTypeUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->reportTypeHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating reportType.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_report_type_delete', methods: ['DELETE'])]
	public function delete(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->reportTypeHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting reportType.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
