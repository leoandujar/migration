<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\TemplateHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Template\GetTemplateRequest;
use App\Apis\CustomerPortal\Http\Request\Template\CreateTemplateRequest;
use App\Apis\CustomerPortal\Http\Request\Template\UpdateTemplateRequest;

#[Route(path: '/templates')]
class TemplateController extends AbstractController
{
	private LoggerService $loggerSrv;
	private TemplateHandler $templateHandler;

	public function __construct(
		TemplateHandler $templateHandler,
		LoggerService $loggerSrv
	) {
		$this->templateHandler = $templateHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_template_list', methods: ['GET'])]
	public function getList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new GetTemplateRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->templateHandler->processGetList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the template list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_template_retrieve', methods: ['GET'])]
	public function getTemplate(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$response = $this->templateHandler->processGetTemplate($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the template.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_template_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CreateTemplateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->templateHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating template.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_template_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateTemplateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->templateHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating template.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_template_delete', methods: ['DELETE'])]
	public function delete(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->templateHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting template.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
