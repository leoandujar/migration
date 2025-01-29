<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\FlowHandler;
use App\Apis\AdminPortal\Http\Request\Flow\FlowCreateRequest;
use App\Apis\AdminPortal\Http\Request\Flow\FlowListRequest;
use App\Apis\AdminPortal\Http\Request\Flow\FlowMonitorListRequest;
use App\Apis\AdminPortal\Http\Request\Flow\FlowRunRequest;
use App\Apis\AdminPortal\Http\Request\Flow\FlowUpdateRequest;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/flows')]
class FlowController extends AbstractController
{
	private FlowHandler $flowHandler;
	private LoggerService $loggerSrv;

	public function __construct(
		FlowHandler $flowHandler,
		LoggerService $loggerSrv,
	) {
		$this->flowHandler = $flowHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_flow_create', methods: ['POST'])]
	public function create(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new FlowCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->flowHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating flow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_flow_delete', methods: ['DELETE'])]
	public function delete(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$response = $this->flowHandler->processDelete($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting flow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/actions', name: 'ap_flow_actions', methods: ['GET'])]
	public function actions(Request $request): ErrorResponse|Response
	{
		try {
			$response = $this->flowHandler->processActions();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving flow actions.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/monitors', name: 'ap_flow_monitor_list', methods: ['GET'])]
	public function getMonitorHistory(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new FlowMonitorListRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->flowHandler->processMonitorHistory($request, $requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting flow monitor.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/monitors/{id}', name: 'ap_flow_monitor_retrieve', methods: ['GET'])]
	public function retrieveMonitor(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$response = $this->flowHandler->retrieveMonitor($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving flow monitor.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_flow_list', methods: ['GET'])]
	public function list(Request $request): ErrorResponse|Response
	{
		try {
			$responseObj = new FlowListRequest($request->query->all());
			if (!$responseObj->isValid()) {
				return $responseObj->getError();
			}
			$response = $this->flowHandler->processList($responseObj->getParams() ?? null);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving flows.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/run/{id}', name: 'ap_flow_run', methods: ['POST'])]
	public function run(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new FlowRunRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->flowHandler->processRun($id, $requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error running flow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_flow_retrieve', methods: ['GET'])]
	public function retrieve(string $id): ErrorResponse|Response
	{
		try {
			$response = $this->flowHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving flow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_flow_update', methods: ['PUT'])]
	public function update(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$requestObj = new FlowUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->flowHandler->processUpdate($id, $requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating flow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
