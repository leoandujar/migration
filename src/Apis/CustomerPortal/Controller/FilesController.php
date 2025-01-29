<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Files\AnalyseFileRequest;
use App\Apis\CustomerPortal\Http\Request\Files\UploadFileRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\FilesHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/files')]
class FilesController extends AbstractController
{
	private LoggerService $loggerSrv;
	private FilesHandler $filesHandler;

	public function __construct(
		FilesHandler $filesHandler,
		LoggerService $loggerSrv
	) {
		$this->filesHandler = $filesHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_files_upload', methods: ['POST'])]
	public function upload(Request $request): ApiResponse
	{
		try {
			$requestObj = new UploadFileRequest($request->files->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->filesHandler->processUpload(
				[
					'params' => $request->files->all(),
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error uploading file.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/analyse', name: 'cp_files_analyse', methods: ['POST'])]
	public function analyse(Request $request): ApiResponse
	{
		try {
			$requestObj = new AnalyseFileRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->filesHandler->processAnalyse($request->getPayload()->all());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error analysing file.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/token', name: 'cp_files_token', methods: ['GET'])]
	public function token(): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->filesHandler->processToken();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting file token.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
