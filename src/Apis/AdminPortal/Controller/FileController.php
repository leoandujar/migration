<?php

namespace App\Apis\AdminPortal\Controller;

use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\AdminPortal\Handlers\FileHandler;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Utils\UploadFileRequest;

#[Route(path: '/files')]
class FileController extends AbstractController
{
	private LoggerService $loggerSrv;
	private FileHandler $fileHandler;

	public function __construct(
		FileHandler $fileHandler,
		LoggerService $loggerSrv,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->fileHandler = $fileHandler;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/temp', name: 'ap_file_temp_upload', methods: ['POST'])]
	public function uploadToBucket(Request $request): ApiResponse
	{
		try {
			$requestObj = new UploadFileRequest(array_merge($request->files->all(), $request->getPayload()->all()));
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->fileHandler->processUploadToBucket($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error uploading file to bucket.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
