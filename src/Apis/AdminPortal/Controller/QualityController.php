<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\QualityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Quality\QualityReportSearchRequest;
use App\Apis\AdminPortal\Http\Request\Quality\QualityReportUpdateRequest;
use App\Apis\AdminPortal\Http\Request\Quality\QualityCategorySearchRequest;
use App\Apis\AdminPortal\Http\Request\Quality\QualityActivitySearchRequest;
use App\Apis\AdminPortal\Http\Request\Quality\QualityReportScoreRequest;

#[Route(path: '/quality')]
class QualityController extends AbstractController
{
	private QualityHandler $qualityHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		QualityHandler $qualityHandler,
		LoggerService $loggerSrv
	) {
		$this->qualityHandler = $qualityHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/categories', name: 'ap_quality_category_list', methods: ['GET'])]
	public function getCategories(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityCategorySearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->qualityHandler->processSearchCategories($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality categories.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/categories/{id}', name: 'ap_quality_category', methods: ['GET'])]
	public function getCategory(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->qualityHandler->processGetChildCategories([
				'id' => $id,
			]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality child categories.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports', name: 'ap_quality_report_list', methods: ['GET'])]
	public function getReports(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityReportSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->qualityHandler->processSearchReports($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports/{id}', name: 'ap_quality_report', methods: ['GET'])]
	public function getReport(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->qualityHandler->processGetReport([
				'id' => $id,
			]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports', name: 'ap_quality_report_create', methods: ['POST'])]
	public function createReport(Request $request): ErrorResponse|Response
	{
		try {
			$response = $this->qualityHandler->processCreateReport($request->getPayload()->all());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports/{id}/issues', name: 'ap_quality_report_issues_update', methods: ['POST'])]
	public function attachIssues(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$params['quality_report_id'] = $id;
			$payload = $request->getPayload()->all();
			$params['quality_issues'] = $payload['issues'] ?? [];
			$response = $this->qualityHandler->processAttachIssues($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error attaching quality issues for report $id.", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports/{id}', name: 'ap_quality_report_delete', methods: ['DELETE'])]
	public function deleteReport(string $id): ErrorResponse|Response
	{
		try {
			$params['quality_report_id'] = $id;
			$response = $this->qualityHandler->processDeleteReport($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting quality report.', $thr);
			$response = new ErrorResponse(null, null, $thr);
		}

		return $response;
	}

	#[Route('/reports/{id}/status', name: 'ap_quality_report_status_update', methods: ['PUT'])]
	public function updateReportStatus(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityReportUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$params = array_merge($requestObj->getParams(), ['quality_report_id' => $id]);
			$response = $this->qualityHandler->processUpdateReportStatus($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports/{id}/score', name: 'ap_quality_report_score', methods: ['GET'])]
	public function calculateReportScore(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityReportScoreRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$params = array_merge($requestObj->getParams(), ['quality_report_id' => $id]);
			$response = $this->qualityHandler->processCalculateReportStatus($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error calculating quality score for report $id", $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reports/activity/{activityId}', name: 'ap_quality_report_by_activity', methods: ['GET'])]
	public function getReportByActivity(Request $request, string $activityId): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityActivitySearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params['activity_id'] = $activityId;
			$params['type'] = $request->query->get('type');
			$response = $this->qualityHandler->processGetReportByActivity($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
