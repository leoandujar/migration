<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\FormSubmissionHandler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Apis\AdminPortal\Http\Request\Forms\FormSubmitRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Forms\FormSubmissionSearchRequest;
use App\Apis\AdminPortal\Http\Request\Forms\FormSubmissionUpdateRequest;
use App\Apis\AdminPortal\Http\Request\Forms\FormSubmissionDownloadRequest;

#[Route(path: '/forms-submission')]
class FormSubmissionController extends AbstractController
{
	private LoggerService $loggerSrv;

	private FormSubmissionHandler $formSubmissionHandler;

	public function __construct(
		FormSubmissionHandler $formSubmissionHandler,
		LoggerService $loggerSrv
	) {
		$this->formSubmissionHandler = $formSubmissionHandler;
		$this->loggerSrv = $loggerSrv;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_form_submission_list', methods: ['GET'])]
	public function search(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new FormSubmissionSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formSubmissionHandler->processSearch($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error searching form submission.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_form_submission_create', methods: ['POST'])]
	public function submit(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new FormSubmitRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formSubmissionHandler->processSubmit($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error submitting form submission.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_form_submission', methods: ['GET'])]
	public function retrieve(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formSubmissionHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving form submissions.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_form_submission_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new FormSubmissionUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->formSubmissionHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating form submission.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/download', name: 'ap_form_submission_download', methods: ['POST'])]
	public function download(Request $request): ApiResponse|BinaryFileResponse|ErrorResponse|null
	{
		try {
			$requestObj = new FormSubmissionDownloadRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formSubmissionHandler->processDownloadViewFile($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error downloading form submission.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/view', name: 'ap_form_submission_view', methods: ['GET'])]
	public function view(string $id): ApiResponse|ErrorResponse|null
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formSubmissionHandler->processViewFile($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error viewing form submission.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_form_submission_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->formSubmissionHandler->processDelete(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting form.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
