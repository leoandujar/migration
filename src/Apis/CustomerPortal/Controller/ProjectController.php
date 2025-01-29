<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\CustomerPortal\Http\Request\Project\CreateProjectV2Request;
use App\Apis\Shared\Http\Request\EmptyPaginationRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\ProjectHandler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Project\GetProjectRequest;
use App\Apis\CustomerPortal\Http\Request\Project\CreateProjectRequest;
use App\Apis\CustomerPortal\Http\Request\Project\SubmitFeedbackRequest;
use App\Apis\CustomerPortal\Http\Request\Project\SubmitComplaintRequest;
use App\Apis\CustomerPortal\Http\Request\Project\AdditionalInstructionRequest;
use App\Apis\CustomerPortal\Http\Request\Project\CancelProjectRequest;
use App\Apis\CustomerPortal\Http\Request\Project\ProjectSubmitExtraFilesRequest;
use App\Apis\CustomerPortal\Http\Request\Project\ProjectAdditionalContactRequest;

#[Route(path: '/projects')]
class ProjectController extends AbstractController
{
	private LoggerService $loggerSrv;
	private ProjectHandler $projectHandler;

	public function __construct(
		ProjectHandler $projectHandler,
		LoggerService $loggerSrv
	) {
		$this->projectHandler = $projectHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('', name: 'cp_project_list', methods: ['GET'])]
	public function getProjects(Request $request): ApiResponse
	{
		try {
			$requestObj = new GetProjectRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetProjects($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving projects.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/export', name: 'cp_project_export', methods: ['POST'])]
	public function exportProjects(Request $request): BinaryFileResponse|ApiResponse
	{
		try {
			$requestObj = new GetProjectRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processExportProjects($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error exporting projects.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'cp_project', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function getProject(int $id): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetProject($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'cp_project_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse
	{
		try {
			$requestObj = new CreateProjectRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/v2', name: 'cp_project_create_v2', methods: ['POST'])]
	public function createV2(Request $request): ApiResponse
	{
		try {
			$requestObj = new CreateProjectV2Request($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processCreateV2($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating project in v2.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/review', name: 'cp_project_review', methods: ['GET'])]
	public function getProjectsReview(Request $request): ApiResponse
	{
		try {
			$requestObj = new EmptyPaginationRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetProjectsReview($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving projects review.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/complaint', name: 'cp_project_complaint_create', methods: ['POST'])]
	public function submitComplaint(Request $request, string $id): ApiResponse
	{
		try {
			$requestObj = new SubmitComplaintRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->projectHandler->processSubmitComplaint($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in project complaint create.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/contacts', name: 'cp_project_contacts_update', methods: ['PUT'])]
	public function additionalContactPerson(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ProjectAdditionalContactRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->projectHandler->processAdditionalContactPerson($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error additional contact person.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/confirmation', name: 'cp_project_confirmation_file', methods: ['GET'])]
	public function confirmationFile(Request $request, string $id): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processConfirmationFile($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in project confirmation file.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/archive', name: 'cp_project_archive', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getArchive(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetArchive($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting project archieve.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/feedback', name: 'cp_project_feedback', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getFeedback(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetFeedback($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving project feedback.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/feedback', name: 'cp_project_feedback_create', methods: ['POST'], requirements: ['id' => '\d+'])]
	public function submitFeedback(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new SubmitFeedbackRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge(['data' => $requestObj->getParams()], ['projectId' => $id]);
			$response = $this->projectHandler->processSubmitFeedback($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error submiting project feedback.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/cancel', name: 'cp_project_cancel', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function cancelProject(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CancelProjectRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge(['data' => $requestObj->getParams()], ['projectId' => $id]);
			$response = $this->projectHandler->processCancelProject($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error canceling project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/instructions', name: 'cp_project_update_instructions', methods: ['PUT'], requirements: ['id' => '\d+'])]
	public function additionalInstructions(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new AdditionalInstructionRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processAdditionalInstructions(array_merge($requestObj->getParams(), ['id' => $id]));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error adding project instructions.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/archive/password', name: 'cp_project_archive_password', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getArchivePassword(Request $request, int $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetArchivePassword($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting project archieve password.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/files', name: 'cp_project_files_create', methods: ['PUT'])]
	public function submitExtraFiles(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ProjectSubmitExtraFilesRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['projectId' => $id]);
			$response = $this->projectHandler->processSubmitExtraFiles($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error submitting extra files to project.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
	
	#[Route('/{id}/files/deliverables', name: 'cp_project_deliverables_file', methods: ['GET'])]
	public function downloadOutputFiles(Request $request, string $id): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processDownloadOutputFiles($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error downloading output files as zip.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}/files/{fileId}', name: 'cp_project_files_file', methods: ['GET'])]
	public function downloadFile(Request $request, string $id, string $fileId): BinaryFileResponse|ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processDownloadFileById($id, $fileId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error downloading file by id.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

}
