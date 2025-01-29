<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Util\UtilsService;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Model\Repository\TaskRepository;
use App\Service\FileSystem\FileSystemService;
use App\Service\RegexService;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TaskReviewHandler extends BaseHandler
{
	private ProjectRepository $projectRepository;
	private CustomerPortalConnector $clientConnector;
	private TaskRepository $taskRepository;
	private FileSystemService $fileSystemSrv;
	private UtilsService $utilsSrv;

	public function __construct(
		ProjectRepository $projectRepository,
		CustomerPortalConnector $clientConnector,
		TaskRepository $taskRepository,
		FileSystemService $fileSystemSrv,
		UtilsService $utilsSrv
	) {
		$this->projectRepository = $projectRepository;
		$this->clientConnector = $clientConnector;
		$this->taskRepository = $taskRepository;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->utilsSrv = $utilsSrv;
	}

	public function processGetTaskReview(array $dataRequest): ApiResponse
	{
		$project = $this->projectRepository->find($dataRequest['projectId']);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$response = $this->clientConnector->getTasksReview($dataRequest['taskId']);

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(data: $response->getRaw());
	}

	public function processCommentTaskReview(array $dataRequest): ApiResponse
	{
		$comment = strip_tags($dataRequest['comment'], RegexService::$htmlTagsAllowed);
		$response = $this->clientConnector->projectCommentTaskReview($dataRequest['taskId'], $comment);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDownloadTaskReviewFile(array $dataRequest)
	{
		$task = $this->taskRepository->find($dataRequest['taskId']);
		if (!$task) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'task');
		}

		$response = $this->clientConnector->projectDownloadTaskReviewFile($dataRequest['taskId']);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$fileBinary = $response->getRaw();
		$extension = '';
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'project_temp_files');
		$filePath = $this->fileSystemSrv->filesPath.'/project_temp_files/file'.uniqid();
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			$mimeType = mime_content_type($filePath);
			if (!empty($mimeType) && $this->utilsSrv->stringContains('/', $mimeType)) {
				$extension = explode('/', $mimeType)[1];
				rename($filePath, "$filePath.$extension");
			}
			$response = new BinaryFileResponse("$filePath.$extension");
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processGetTaskReviewFiles(array $dataRequest): ApiResponse
	{
		$project = $this->projectRepository->find($dataRequest['projectId']);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$response = $this->clientConnector->getFilesTaskReview($dataRequest['taskId']);

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(data: $response->getRaw());
	}

	public function processUploadFilesTaskReview(array $dataRequest)
	{
		$task = $this->taskRepository->find($dataRequest['taskId']);
		if (!$task) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'task');
		}

		$params = $dataRequest['params'];

		/** @var UploadedFile $file */
		$file = $params['file'];
		$processParams = [];
		foreach ($params as $key => $requestParam) {
			$processParams[] = [
				'name' => $key,
				'filename' => $file->getClientOriginalName(),
				'contents' => file_get_contents($file->getPathname()),
			];
		}

		$response = $this->clientConnector->uploadFilesTaskReview(
			$dataRequest['taskId'],
			$processParams
		);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDeleteFilesTaskReview(array $dataRequest)
	{
		$task = $this->taskRepository->find($dataRequest['taskId']);
		if (!$task) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
				'task'
			);
		}

		$response = $this->clientConnector->deleteFilesTaskReview(
			$dataRequest['taskId'],
			$dataRequest['fileName']
		);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
