<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Http\Request\CategoryGroup\CategoryGroupAssignRequest;
use App\Apis\AdminPortal\Http\Request\CategoryGroup\CategoryGroupCreateRequest;
use App\Apis\AdminPortal\Http\Request\CategoryGroup\CategoryGroupSearchRequest;
use App\Apis\AdminPortal\Http\Request\CategoryGroup\CategoryGroupUpdateRequest;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\AdminPortal\Handlers\CategoryGroupHandler;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/category-groups')]
class CategoryGroupController extends AbstractController
{
	private LoggerService $loggerSrv;

	private CategoryGroupHandler $categoryGroupHandler;

	public function __construct(
		LoggerService $loggerSrv,
		CategoryGroupHandler $categoryGroupHandler
	) {
		$this->loggerSrv = $loggerSrv;

		$this->categoryGroupHandler = $categoryGroupHandler;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('', name: 'ap_category_group_list', methods: ['GET'])]
	public function getList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CategoryGroupSearchRequest($request->query->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processGetList($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the category group list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_category_group', methods: ['GET'])]
	public function retrieve(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processRetrieve($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting the category group.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/assign/customer/{customerId}', name: 'ap_category_group_customer_update', methods: ['PUT'])]
	public function assignToCustomer(string $customerId, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CategoryGroupAssignRequest(array_merge($request->getPayload()->all(), ['id' => $customerId]));
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processAssignToCustomer($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error assigning category group to customer.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/assign/template/{templateId}', name: 'ap_category_group_template_update', methods: ['PUT'])]
	public function assignToTemplate(string $templateId, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CategoryGroupAssignRequest(array_merge($request->getPayload()->all(), ['id' => $templateId]));
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processAssignToTemplate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error assigning category group to template.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/assign/workflow/{workflowId}', name: 'ap_category_group_workflow_update', methods: ['PUT'])]
	public function assignToWorkflow(string $workflowId, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CategoryGroupAssignRequest(array_merge($request->getPayload()->all(), ['id' => $workflowId]));
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processAssignToWorkflow($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error assigning category group to workflow.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('', name: 'ap_category_group_create', methods: ['POST'])]
	public function create(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CategoryGroupCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processCreate($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error creating category group.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_category_group_update', methods: ['PUT'])]
	public function update(string $id, Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new CategoryGroupUpdateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['id' => $id]);
			$response = $this->categoryGroupHandler->processUpdate($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating category group.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/{id}', name: 'ap_category_group_delete', methods: ['DELETE'])]
	public function delete(string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->categoryGroupHandler->processDelete(['id' => $id]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error deleting category group.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
