<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\ReportHandler;
use App\Apis\AdminPortal\Http\Request\Report\GenerateReportFromTemplateRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/reports/customer')]
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
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/{id}/templates', name: 'ap_report_customer_template_list', methods: ['GET'])]
	public function list(string $id): ErrorResponse|ApiResponse
	{
		try {
			$response = $this->reportHandler->processList($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting reportTemplate list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/generate/{reportId}', name: 'ap_report_customer_generate', methods: ['POST'])]
	public function generate(Request $request, string $id, string $reportId = null): ApiResponse|ErrorResponse|BinaryFileResponse|Response
	{
		try {
			$requestObj = new GenerateReportFromTemplateRequest(array_merge($request->getPayload()->all(), ['customer_id' => $id], ['id' => $reportId]));
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
