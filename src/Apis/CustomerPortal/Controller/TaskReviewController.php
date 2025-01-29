<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Handlers\TaskReviewHandler;
use App\Apis\CustomerPortal\Http\Request\Project\ProjectTaskReviewRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Apis\CustomerPortal\Http\Request\Files\UploadFileRequest;

#[Route(path: '/projects/{id}/tasks/{taskId}/review')]
class TaskReviewController extends AbstractController
{
	private TaskReviewHandler $taskReviewHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		TaskReviewHandler $taskReviewHandler,
		LoggerService $loggerSrv
	) {
		$this->taskReviewHandler = $taskReviewHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_project_task_review', methods: ['GET'])]
	public function getTaskReview(Request $request, string $id, string $taskId): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskReviewHandler->processGetTaskReview(
				[
					'projectId' => $id,
					'taskId' => $taskId,
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving task review.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_project_task_review_update', methods: ['PUT'])]
	public function taskReview(Request $request, string $id, string $taskId): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ProjectTaskReviewRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge(
				$requestObj->getParams(),
				[
					'projectId' => $id,
					'taskId' => $taskId,
				]
			);
			$response = $this->taskReviewHandler->processCommentTaskReview($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error processing comment task review.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/files', name: 'cp_project_task_review_files', methods: ['GET'])]
	public function downloadTaskReviewFile(Request $request, string $id, string $taskId): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskReviewHandler->processDownloadTaskReviewFile(
				[
					'projectId' => $id,
					'taskId' => $taskId,
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error downloading file by id.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/files/reviewed', name: 'cp_project_task_reviewed_files_list', methods: ['GET'])]
	public function getTaskReviewFiles(Request $request, string $id, string $taskId): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskReviewHandler->processGetTaskReviewFiles(
				[
					'projectId' => $id,
					'taskId' => $taskId,
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving task review.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/files/reviewed', name: 'cp_project_task_review_files_create', methods: ['POST'])]
	public function uploadFilesTaskReview(Request $request, string $taskId): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new UploadFileRequest($request->files->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskReviewHandler->processUploadFilesTaskReview(
				[
					'params' => $request->files->all(),
					'taskId' => $taskId,
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error uploading files for task review.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/files/reviewed/{fileName}', name: 'cp_project_task_review_files_delete', methods: ['DELETE'])]
	public function deleteFilesTaskReview(Request $request, string $taskId, string $fileName): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskReviewHandler->processDeleteFilesTaskReview(
				[
					'fileName' => $fileName,
					'taskId' => $taskId,
				]
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting files for task review.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
