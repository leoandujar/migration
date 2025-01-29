<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\ReportTemplateHandler;
use App\Apis\AdminPortal\Http\Request\ReportTemplate\ReportTemplateCreateRequest;
use App\Apis\AdminPortal\Http\Request\ReportTemplate\ReportTemplateSearchRequest;
use App\Apis\AdminPortal\Http\Request\ReportTemplate\ReportTemplateUpdateRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/reports/templates')]
class ReportTemplateController extends AbstractController
{
	private ReportTemplateHandler $reportTemplateHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		ReportTemplateHandler $reportTemplateHandler,
		LoggerService $loggerSrv
	) {
		$this->reportTemplateHandler = $reportTemplateHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_report_template_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|ApiResponse
	{
		try {
			$requestObj = new ReportTemplateSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->reportTemplateHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting reportTemplate list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_report_template_retrieve', methods: ['GET'])]
	public function retrieve(string $id): ErrorResponse|ApiResponse
	{
		try {
			$response = $this->reportTemplateHandler->retrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving reportTemplate.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_report_template_create', methods: ['POST'])]
	public function create(Request $request): ErrorResponse|ApiResponse
	{
		try {
			$requestObj = new ReportTemplateCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->reportTemplateHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating reportTemplate.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_report_template_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ReportTemplateUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->reportTemplateHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating reportTemplate.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_report_template_delete', methods: ['DELETE'])]
	public function delete(string $id): ErrorResponse|ApiResponse
	{
		try {
			$response = $this->reportTemplateHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting reportTemplate.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
