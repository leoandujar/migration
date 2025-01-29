<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\FileSystem\FileSystemService;
use App\Apis\Shared\Util\Factory;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Connector\Xtrf\XtrfConnector;
use App\Model\Entity\WorkflowJobFile;
use App\Model\Repository\ProjectRepository;
use App\Service\Notification\NotificationService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskHandler extends BaseHandler
{
	private ProjectRepository $projectRepository;
	private CustomerPortalConnector $clientConnector;
	private FileSystemService $fileSystemSrv;
	private XtrfConnector $xtrfConn;
	private NotificationService $notificationSrv;
	private RequestStack $requestStack;
	private SessionInterface $session;
	private EntityManagerInterface $em;

	public function __construct(
		ProjectRepository $projectRepository,
		FileSystemService $fileSystemSrv,
		XtrfConnector $xtrfConnector,
		NotificationService $notificationSrv,
		CustomerPortalConnector $clientConnector,
		RequestStack $requestStack,
		EntityManagerInterface $em,
	) {
		parent::__construct($requestStack, $em);
		$this->session = $requestStack->getSession();
		$this->projectRepository = $projectRepository;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->xtrfConn = $xtrfConnector;
		$this->notificationSrv = $notificationSrv;
		$this->clientConnector = $clientConnector;
		$this->requestStack = $requestStack;
	}

	public function processGetTasks(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$result = [];
		foreach ($project->getTasks()->getValues() as $task) {
			$result[] = Factory::taskDtoInstance($task);
		}

		return new ApiResponse(data: $result);
	}

	public function processAddAdditionalTask(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$project = $this->projectRepository->find($dataRequest['projectId']);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		/** @var Task $task */
		$task = $project->getTasks()->first();

		if (!$task) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'task');
		}

		$response = $this->clientConnector->tasksDownloadInputFiles($task->getId());

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$fileBinary = $response->getRaw();
		$subFolderZip = "project_tasks_files/zip_files/{$project->getId()}/{$task->getId()}";
		$subFolderUnZip = "project_tasks_files/unzip_files/{$project->getId()}/{$task->getId()}";
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $subFolderZip);
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $subFolderUnZip);
		$fileName = uniqid().'.zip';
		$filePath = $this->fileSystemSrv->filesPath."/$subFolderZip/$fileName";
		if (!$this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_UNABLE_CREATE_FILE,
				ApiError::$descriptions[ApiError::CODE_UNABLE_CREATE_FILE]
			);
		}

		$unZipSuccess = $this->fileSystemSrv->unzipFile($filePath, $this->fileSystemSrv->filesPath."/$subFolderUnZip/");
		if (!$unZipSuccess) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_ERROR_UNZIP,
				ApiError::$descriptions[ApiError::CODE_ERROR_UNZIP]
			);
		}

		$filesTokens = [];
		$filesList = scandir($this->fileSystemSrv->filesPath."/$subFolderUnZip");
		foreach ($filesList as $name) {
			// avoiding . and .. for normal filesystem
			if (mb_strlen($name) > 2) {
				$currentFilePath = $this->fileSystemSrv->filesPath."/$subFolderUnZip/$name";
				$uploadResponse = $this->xtrfConn->uploadProjectFile(
					[
						[
							'name' => 'file',
							'contents' => $this->fileSystemSrv->getBinaryFromFile($currentFilePath),
							'filename' => $name,
						],
					]
				);

				if (!$uploadResponse->isSuccessfull()) {
					return new ErrorResponse(
						Response::HTTP_BAD_GATEWAY,
						ApiError::CODE_UNABLE_UPLOAD_FILE,
						ApiError::$descriptions[ApiError::CODE_UNABLE_UPLOAD_FILE]
					);
				}
				$filesTokens[] = [
					'token' => $uploadResponse->getToken(),
					'category' => WorkflowJobFile::CATEGORY_WORKFILE,
				];
			}
		}

		$dataCreate = [
			'specializationId' => $project->getSpecialization()?->getId(),
			'workflowId' => $project->getWorkflow()?->getId(),
			'name' => $project->getName(),
			'languageCombination' => [
				'sourceLanguageId' => $dataRequest['source_language'],
				'targetLanguageId' => $dataRequest['target_language'],
			],
			'dates' => [
				'startDate' => ['time' => (new \DateTime('now'))->getTimestamp() * 1000],
			],
			'files' => $filesTokens,
		];

		$createResponse = $this->xtrfConn->createAdditionalTaskRequest($project->getId(), $dataCreate);

		if (!$createResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$data = [
			'subject' => 'AvantPortal: new action on project '.$project->getIdNumber().': Added new language combination',
			'template' => 'message',
			'data' => [
				'title' => 'Added new language combination',
				'message' => 'Source language: '.$dataRequest['source_language'].'. Target language: '.$dataRequest['target_language'],
				'contact' => [
					'name' => $user->getName().' '.$user->getLastName(),
					'customer' => $this->getCurrentCustomer()?->getName(),
				],
				'project' => [
					'idNumber' => $project->getIdNumber(),
					'id' => $project->getId(),
				],
			],
		];

		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			$project->getProjectManager()->getEmail(),
			$data
		);

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDownloadTasksOutputFiles(string $projectId, string $taskId): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		$project = $this->projectRepository->find($projectId);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}
		if ($project->getArchivedAt()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'project archived');
		}

		$response = $this->clientConnector->projectTasksDownloadOutputFiles($taskId);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$fileBinary = $response->getRaw();
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'project_temp_files');
		$filePath = $this->fileSystemSrv->filesPath.'/project_temp_files/task_file'.uniqid().'.zip';
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
