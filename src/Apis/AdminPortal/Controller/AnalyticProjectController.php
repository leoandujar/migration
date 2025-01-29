<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\AnalyticProjectHandler;
use App\Apis\AdminPortal\Http\Request\AnalyticProject\AnalyticProjectSearchRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/analytic-projects')]
class AnalyticProjectController extends AbstractController
{
	private AnalyticProjectHandler $analyticProHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		AnalyticProjectHandler $analyticProHandler,
		LoggerService $loggerSrv
	) {
		$this->analyticProHandler = $analyticProHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_analytic_project_search', methods: ['GET'])]
	public function search(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AnalyticProjectSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->analyticProHandler->processSearch($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error searching analytic project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_analytic_project_retrieve', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function retrieve(int $id): ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->analyticProHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving analytic project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_analytic_project_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->analyticProHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating form submission.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/fields', name: 'ap_analytic_project_fields', methods: ['GET'])]
	public function listFields(): ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->analyticProHandler->processFieldList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving analytic project class fields.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
