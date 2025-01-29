<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Command\Command\CustomerportalFilesProjectsProcessCommand;
use App\Connector\ApacheTika\TikaConnector;
use App\Linker\Services\RedisClients;
use App\Service\FileSystem\FileSystemService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Service\FileSystem\AzureFileSystemService;
use Doctrine\ORM\EntityManagerInterface;

class FilesHandler extends BaseHandler
{
	private SessionInterface $session;
	private TokenStorageInterface $tokenStorage;
	private CustomerPortalConnector $clientConnector;
	private RedisClients $redisClients;
	private RequestStack $requestStack;
	private FileSystemService $fileSystemSrv;
	private TikaConnector $tikaConnector;
	private AzureFileSystemService $azureFileSystemService;
	private EntityManagerInterface $em;

	public function __construct(
		RequestStack $requestStack,
		RedisClients $redisClients,
		TokenStorageInterface $tokenStorage,
		CustomerPortalConnector $clientConnector,
		FileSystemService $fileSystemSrv,
		TikaConnector $tikaConnector,
		AzureFileSystemService $azureFileSystemService,
		EntityManagerInterface $em,
	) {
		parent::__construct($requestStack, $em);
		$this->tokenStorage = $tokenStorage;
		$this->session = $requestStack->getSession();
		$this->clientConnector = $clientConnector;
		$this->redisClients = $redisClients;
		$this->requestStack = $requestStack;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->tikaConnector = $tikaConnector;
		$this->azureFileSystemService = $azureFileSystemService;
	}

	public function processUpload(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();

		$params = $dataRequest['params'];
		/** @var UploadedFile $file */
		$file = $params['file'];
		$uploadId = uniqid('file_').hrtime(true);
		$today = (new \DateTime('now'))->format('Y-m-d');
		$filePath = $today.DIRECTORY_SEPARATOR.
			$file->getClientOriginalName();
		$toPath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.
			$customer->getId().DIRECTORY_SEPARATOR.
			$user->getId().DIRECTORY_SEPARATOR.$filePath;
		$this->fileSystemSrv->createDirectory(
			$this->fileSystemSrv->filesPath,
			$customer->getId().DIRECTORY_SEPARATOR.
			$user->getId().DIRECTORY_SEPARATOR.$today
		);
		if (!copy($file->getRealPath(), $toPath)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_UNABLE_UPLOAD_FILE,
				ApiError::$descriptions[ApiError::CODE_UNABLE_UPLOAD_FILE]
			);
		}
		$data = (object) [
			'Key' => $uploadId,
			'EntityName' => CustomerportalFilesProjectsProcessCommand::TYPE_CP_PROJECT_EXTRA_FILES,
			'FilePath' => $toPath,
			'FileName' => $file->getClientOriginalName(),
			'FileSize' => $file->getSize(),
			'Token' => null,
			'CreatedAt' => (new \DateTime('now'))->getTimestamp(),
		];
		$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_PENDING_FILES, [$uploadId => serialize($data)]);
		$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, microtime(true), $uploadId);

		return new ApiResponse(
			data: [
				'id' => $uploadId,
				'path' => $filePath,
			]
		);
	}

	public function processAnalyse(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();

		$filePath = $dataRequest['path'];
		$toPath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.
			$customer->getId().DIRECTORY_SEPARATOR.
			$user->getId().DIRECTORY_SEPARATOR.
			$filePath;

		if (!file_exists($toPath)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
				'filepath'
			);
		}

		$settings = $customer->getSettings()->getProjectSettings();
		$analyze = $settings->isAnalyzeFiles();
		$maxFileSize = 52428800;
		$fileStats = [
			'words' => 0,
			'analyzed' => false,
		];

		if ($analyze && filesize($toPath) <= $maxFileSize) {
			$fileStats = $this->tikaConnector->getFileStatsV2($toPath);
		}

		return new ApiResponse(data: $fileStats);
	}

	public function processToken(): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();

		$path = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.
			$customer->getId().DIRECTORY_SEPARATOR.
			$user->getId();

		$token = $this->azureFileSystemService->generateAzureSasToken($path);

		return new ApiResponse(data: ['token' => $token, 'path' => $path]);
	}
}
