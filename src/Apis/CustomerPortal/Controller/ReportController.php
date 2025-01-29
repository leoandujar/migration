<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Report\GenerateReportFromTemplateRequest;
use App\Apis\CustomerPortal\Http\Request\Report\ReportTemplateSearchRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\ReportHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/reports')]
class ReportController extends AbstractController
{
	private LoggerService $loggerSrv;
	private ReportHandler $reportHandler;

	public function __construct(
		ReportHandler $reportHandler,
		LoggerService $loggerSrv
	) {
		$this->reportHandler = $reportHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('/templates', name: 'cp_report_template_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|ApiResponse
	{
		try {
			$requestObj = new ReportTemplateSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->reportHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting reportTemplate list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/generate/{id}', name: 'cp_report_generate', methods: ['POST'])]
	public function generate(Request $request, string $id = null): ApiResponse|ErrorResponse|BinaryFileResponse|Response
	{
		try {
			$requestObj = new GenerateReportFromTemplateRequest(array_merge($request->getPayload()->all(), ['id' => $id]));
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->reportHandler->processGenerateReport($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error generating report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
