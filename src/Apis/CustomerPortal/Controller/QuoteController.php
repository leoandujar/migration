<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Project\AdditionalTaskRequest;
use App\Apis\CustomerPortal\Http\Request\Project\ProjectSubmitExtraFilesRequest;
use App\Apis\CustomerPortal\Http\Request\Quote\RejectRequest;
use App\Apis\CustomerPortal\Http\Request\Quote\UpdateQuoteRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\QuoteHandler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Apis\CustomerPortal\Http\Request\Quote\GetQuoteRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Quote\CreateQuoteRequest;
use App\Apis\CustomerPortal\Http\Request\Quote\AcceptDeclineRequest;

#[Route(path: '/quotes')]
class QuoteController extends AbstractController
{
	private LoggerService $loggerSrv;
	private QuoteHandler $quoteHandler;

	public function __construct(
		QuoteHandler $quoteHandler,
		LoggerService $loggerSrv
	) {
		$this->quoteHandler = $quoteHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_quote_list', methods: ['GET'])]
	public function getQuotes(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new GetQuoteRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processGetQuotes($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving quotes.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/export', name: 'cp_quote_export', methods: ['POST'])]
	public function exportQuotes(Request $request): BinaryFileResponse|ApiResponse
	{
		try {
			$requestObj = new GetQuoteRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processExportQuotes($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error exporting quotes.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_quote', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function getQuote(int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processGetQuote($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/confirmation', name: 'cp_quote_confirmation_file', methods: ['GET'])]
	public function confirmationFile(Request $request, string $id): ApiResponse|ErrorResponse|BinaryFileResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processConfirmationFile($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in quote confirmation file.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/approve', name: 'cp_quote_approve', methods: ['PUT'])]
	public function approve(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processApprove($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in approve quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/reject', name: 'cp_quote_reject', methods: ['PUT'])]
	public function reject(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new RejectRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->quoteHandler->processReject($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in reject quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/action', name: 'cp_quote_action', methods: ['POST'])]
	public function acceptDecline(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AcceptDeclineRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->quoteHandler->processAcceptDecline($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in accept decline quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/files', name: 'cp_quote_upload_additional', methods: ['PUT'])]
	public function uploadExtraFiles(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ProjectSubmitExtraFilesRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['quoteId' => $id]);
			$response = $this->quoteHandler->processSubmitExtraFiles($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error submitting extra files to quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/files/{fileId}', name: 'cp_quote_files_file', methods: ['GET'])]
	public function downloadFile(Request $request, string $id, string $fileId): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processDownloadFileById($id, $fileId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error downloading quote file by id.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/tasks', name: 'cp_quote_task_create', methods: ['POST'])]
	public function addAdditionalTask(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AdditionalTaskRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['quoteId' => $id]);
			$response = $this->quoteHandler->processAddAdditionalTask($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding new task to quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_quote_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CreateQuoteRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->quoteHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_quote_update', methods: ['PUT'])]
	public function update(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UpdateQuoteRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['quoteId' => $id]);
			$response = $this->quoteHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating quote.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
