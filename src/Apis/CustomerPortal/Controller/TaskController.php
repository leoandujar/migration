<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Handlers\TaskHandler;
use App\Apis\CustomerPortal\Http\Request\Project\AdditionalTaskRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route(path: '/projects/{id}/tasks')]
class TaskController extends AbstractController
{
	private TaskHandler $taskHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		TaskHandler $taskHandler,
		LoggerService $loggerSrv
	) {
		$this->taskHandler = $taskHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_project_tasks', methods: ['GET'])]
	public function getTasks(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskHandler->processGetTasks($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving tasks.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_project_task_create', methods: ['POST'])]
	public function addAdditionalTask(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AdditionalTaskRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['projectId' => $id]);
			$response = $this->taskHandler->processAddAdditionalTask($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding new task to project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{taskId}/files/deliverables', name: 'cp_project_task_files_deliverables_file', methods: ['GET'])]
	public function downloadTasksOutputFiles(Request $request, string $id, string $taskId): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->taskHandler->processDownloadTasksOutputFiles($id, $taskId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error downloading tasks output files as zip.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
