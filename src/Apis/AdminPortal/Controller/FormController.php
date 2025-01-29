<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\AdminPortal\Handlers\FormHandler;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Http\Request\Forms\FormCreateRequest;
use App\Apis\AdminPortal\Http\Request\Forms\FormUpdateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/forms')]
class FormController extends AbstractController
{
	private LoggerService $loggerSrv;
	private FormHandler $formHandler;

	public function __construct(
		FormHandler $formHandler,
		LoggerService $loggerSrv
	) {
		$this->formHandler = $formHandler;
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_form_list', methods: ['GET'])]
	public function getList(): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formHandler->processGetList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the form list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_form', methods: ['GET'])]
	public function retrieve(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving form.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_form_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new FormCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formHandler->processCreate($request, $requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating form.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_form_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new FormUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->formHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating form.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_form_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formHandler->processDelete(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting form.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
