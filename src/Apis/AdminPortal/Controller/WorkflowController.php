<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\WorkflowHandler;
use App\Apis\AdminPortal\Http\Request\Workflows\WorkflowCreateRequest;
use App\Apis\AdminPortal\Http\Request\Workflows\WorkflowMonitorListRequest;
use App\Apis\AdminPortal\Http\Request\Workflows\WorkflowRunRequest;
use App\Apis\AdminPortal\Http\Request\Workflows\WorkflowSearchRequest;
use App\Apis\AdminPortal\Http\Request\Workflows\WorkflowUpdateRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/workflows')]
class WorkflowController extends AbstractController
{
	private WorkflowHandler $workflowHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		WorkflowHandler $workflowHandler,
		LoggerService $loggerSrv,
	) {
		$this->workflowHandler = $workflowHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_workflow_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new WorkflowSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->workflowHandler->processList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting workflow list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_workflow_create', methods: ['POST'])]
	public function create(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new WorkflowCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->workflowHandler->processCreate($request, $requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_workflow_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new WorkflowUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->workflowHandler->processUpdate($request, $params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_workflow_delete', methods: ['DELETE'])]
	public function delete(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$response = $this->workflowHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/run/{id}', name: 'ap_workflow_run', methods: ['POST'])]
	public function run(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new WorkflowRunRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->workflowHandler->processRun($request, array_merge(['id' => $id], $requestObj->getParams()));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error running workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/dispatch/{id}', name: 'ap_workflow_dispatch', methods: ['POST'])]
	public function dispatch(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new WorkflowRunRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->workflowHandler->processDispatch($request, array_merge(['id' => $id], $requestObj->getParams()));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error dispatching workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/monitor', name: 'ap_workflow_monitor_history', methods: ['GET'])]
	public function getMonitorHistory(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new WorkflowMonitorListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->workflowHandler->processMonitorHistory($request, $requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting workflow monitor.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/monitor/{id}', name: 'ap_workflow_monitor_retrieve', methods: ['GET'])]
	public function retrieveMonitor(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$response = $this->workflowHandler->retrieveMonitor($request, $id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving workflow monitor.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_workflow_retrieve', methods: ['GET'])]
	public function retrieve(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$response = $this->workflowHandler->retrieve($request, $id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
