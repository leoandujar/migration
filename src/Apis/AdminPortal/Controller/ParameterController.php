<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\ParameterHandler;
use App\Apis\AdminPortal\Http\Request\Parameter\CreateParameterRequest;
use App\Apis\AdminPortal\Http\Request\Parameter\ParameterListRequest;
use App\Apis\AdminPortal\Http\Request\Parameter\UpdateParameterRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/parameters')]
class ParameterController extends AbstractController
{
	private LoggerService $loggerSrv;

	private ParameterHandler $parameterHandler;

	public function __construct(
		LoggerService $loggerSrv,
		ParameterHandler $parameterHandler,
	) {
		$this->loggerSrv = $loggerSrv;

		$this->parameterHandler = $parameterHandler;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_parameter_list', methods: ['GET'])]
	public function getList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ParameterListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->parameterHandler->processGetList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the parameters list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_parameter_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CreateParameterRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->parameterHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating parameter.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_parameter_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateParameterRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->parameterHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating parameter.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_parameter_delete', methods: ['DELETE'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->parameterHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting parameter.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
