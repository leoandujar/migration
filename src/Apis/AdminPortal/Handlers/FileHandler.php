<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Traits\UserResolver;
use App\Model\Entity\InternalUser;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FileHandler
{
	use UserResolver;

	private LoggerService $loggerSrv;
	private CloudFileSystemService $fileSystemSrv;
	private SecurityHandler $securityHandler;
	private TokenStorageInterface $tokenStorage;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		LoggerService $loggerSrv,
		SecurityHandler $securityHandler,
		TokenStorageInterface $tokenStorage,
		ParameterBagInterface $parameterBag,
		CloudFileSystemService $fileSystemSrv,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->tokenStorage = $tokenStorage;
		$this->parameterBag = $parameterBag;
		$this->securityHandler = $securityHandler;
		$this->fileSystemSrv = $fileSystemSrv;
	}

	public function processUploadToBucket(array $dataRequest): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		/** @var UploadedFile $file */
		$file = $dataRequest['file'];
		$fileName = $file->getClientOriginalName();
		$filePath = $file->getRealPath();
		$bucketPath = $this->parameterBag->get('app.files.temp.path')."/{$user->getId()}";
		if (!empty($dataRequest['reference'])) {
			$bucketPath .= "/{$dataRequest['reference']}";
		} else {
			$bucketPath .= '/'.date('Y-m-d-H-i');
		}

		$this->fileSystemSrv->changeStorage($this->parameterBag->get('app.files.temp.bucket'));
		if (!$this->fileSystemSrv->upload("$bucketPath/$fileName", $filePath)) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_UNABLE_UPLOAD_FILE, ApiError::$descriptions[ApiError::CODE_UNABLE_UPLOAD_FILE]);
		}
		$publicUrl = $this->fileSystemSrv->getTemporaryUrl("$bucketPath/$fileName");

		return new ApiResponse(data: [
			'path' => "$bucketPath/$fileName",
			'fileName' => $fileName,
			'folder' => $bucketPath,
			'url' => $publicUrl,
		]);
	}
}
