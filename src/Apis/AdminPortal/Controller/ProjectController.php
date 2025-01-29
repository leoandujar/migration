<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Http\Request\Projects\CreateProjectRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\ProjectHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Projects\ProjectListRequest;

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
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_project_list', methods: ['GET'])]
	public function getProjects(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ProjectListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->projectHandler->processGetProjects($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving project list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_project', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getProject(int $id): ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->projectHandler->processGetProject($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving project list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_project_create', methods: ['POST'])]
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

	#[Route('/{id}/archive', name: 'ap_project_archive', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getArchive(int $id): ApiResponse|ErrorResponse
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

	#[Route('/{id}/archive/password', name: 'ap_project_archive_password', methods: ['GET'], requirements: ['id' => '\d+'])]
	public function getArchivePassword(int $id): ApiResponse|ErrorResponse
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
}
