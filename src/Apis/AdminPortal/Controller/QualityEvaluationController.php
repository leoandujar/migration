<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\QualityEvaluationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Quality\QualityReportSearchRequest;
use App\Apis\AdminPortal\Http\Request\Quality\QualityEvaluationRequest;

#[Route(path: '/quality/evaluations')]
class QualityEvaluationController extends AbstractController
{
	private QualityEvaluationHandler $evaluationHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		QualityEvaluationHandler $evaluationHandler,
		LoggerService $loggerSrv
	) {
		$this->evaluationHandler = $evaluationHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_quality_evaluation_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityReportSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->evaluationHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality evaluation report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_quality_evaluation_retrieve', methods: ['GET'])]
	public function retrieve(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->evaluationHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_quality_evaluation_create', methods: ['POST'])]
	public function create(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new QualityEvaluationRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->evaluationHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating quality report.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_quality_evaluation_delete', methods: ['DELETE'])]
	public function delete(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$params['id'] = $id;
			$response = $this->evaluationHandler->processDelete($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting quality evaluation.', $thr);
			$response = new ErrorResponse(null, null, $thr);
		}

		return $response;
	}
}
