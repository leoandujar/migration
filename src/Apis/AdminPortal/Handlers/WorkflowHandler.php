<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\AdminPortal\Http\Response\Workflow\WorkflowMonitorResponse;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\DTO\GenericPersonDto;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\DTO\WorkflowDto;
use App\Apis\Shared\DTO\WorkflowMonitorDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Constant\DateConstant;
use App\Linker\Services\RedisClients;
use App\Message\WorkflowRunMessage;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\InternalUser;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\CategoryGroupRepository;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Model\Repository\WorkflowParamsRepository;
use App\Model\Repository\WorkflowRepository;
use App\Constant\Constants;
use Cron\CronExpression;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class WorkflowHandler
{
	private EntityManagerInterface $em;
	private WorkflowRepository $wfRepo;
	private RedisClients $redisClients;
	private SecurityHandler $securityHandler;
	private WorkflowParamsRepository $wfpRepo;
	private InternalUserRepository $userRepo;
	private WorkflowMonitorRepository $wfmRepo;
	private CategoryGroupRepository $categoryGroupRepo;
	private MessageBusInterface $bus;

	public function __construct(
		EntityManagerInterface $em,
		WorkflowRepository $wfRepo,
		RedisClients $redisClients,
		WorkflowParamsRepository $wfpRepo,
		WorkflowMonitorRepository $wfmRepo,
		SecurityHandler $securityHandler,
		InternalUserRepository $userRepo,
		CategoryGroupRepository $categoryGroupRepo,
		MessageBusInterface $bus,
	) {
		$this->em = $em;
		$this->wfRepo = $wfRepo;
		$this->wfpRepo = $wfpRepo;
		$this->securityHandler = $securityHandler;
		$this->redisClients = $redisClients;
		$this->userRepo = $userRepo;
		$this->wfmRepo = $wfmRepo;
		$this->categoryGroupRepo = $categoryGroupRepo;
		$this->bus = $bus;
	}

	public function processList(array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$userCategoryGroups = $user->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}

		$search = $params['search'] ?? null;
		$notificationType = $params['notification_type'] ?? null;
		$workflowType = $params['workflow_type'] ?? [];
		$runAutomatically = $params['run_automatically'] ?? null;
		$runPattern = $params['run_pattern'] ?? null;
		$totalRows = $this->wfRepo->getCountRows();
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$dataQuery = array_merge([
			'start' => $paginationDto->from,
			'workflow_type' => $workflowType,
			'notification_type' => $notificationType,
			'run_automatically' => $runAutomatically,
			'run_pattern' => $runPattern,
			'category_groups' => $userCategoryGroups,
			'search' => $search,
		], $params);
		$result = $this->wfRepo->getSearch($dataQuery);

		$response = new DefaultPaginationResponse(
			[
				'entities' => $this->prepareWfListResponse($result),
			]
		);

		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function retrieve(Request $request, string $id): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$workflow = $this->wfRepo->find($id);

		if (!$workflow) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
		}

		$userCategoryGroups = $user->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}
		$haveGroup = false;
		foreach ($userCategoryGroups as $userCategoryGroup) {
			if (in_array($userCategoryGroup, $workflow->getCategoryGroupsCodes())) {
				$haveGroup = true;
			}
		}

		if (!$haveGroup) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		return new ApiResponse(data: Factory::workflowDtoInstance($workflow));
	}

	public function processCreate(Request $request, array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$workflow = $this->wfRepo->findBy(['name' => $params['name']]);
		if ($workflow) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}

		if (!in_array($params['notification_type'], Constants::getWfNotificationTypes())) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'notification_type');
		}

		if (isset($params['workflow_type']) && !in_array($params['workflow_type'], Constants::getWfTypes())) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'workflow_type');
		}

		if (empty($params['params'])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'params');
		}

		$workflow = new WFWorkflow();
		$workflowParams = new WFParams();

		$wfpId = $user->getId() + 5000;
		$wfId = $user->getId() + 5000;
		$lastId = $this->wfpRepo->getHigherId();
		$lastWfId = $this->wfRepo->getHigherId();
		if ($lastId) {
			$wfpId = $lastId + $user->getId();
		}
		if ($lastWfId) {
			$wfId = $lastWfId + $user->getId();
		}

		$workflowParams
			->setId($wfpId)
			->setNotificationType($params['notification_type'])
			->setNotificationTarget($params['notification_target'] ?? null)
			->setParams($params['params']);

		$runPattern = $params['run_pattern'] ?? null;
		if (null !== $runPattern) {
			try {
				new CronExpression($runPattern);
			} catch (\Throwable) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'run_pattern');
			}
		}

		$workflow
			->setId($wfId)
			->setName(strip_tags($params['name']))
			->setDescription($params['description'] ? strip_tags($params['description']) : null)
			->setType($params['workflow_type'] ? strval($params['workflow_type']) : null)
			->setRunAutomatically($params['run_automatically'] ? boolval($params['run_automatically']) : false)
			->setRunPattern($runPattern)
			->setParameters($workflowParams)
			->setCreatedAt(new \DateTime('now'));

		if (!empty($params['category_groups'])) {
			foreach ($params['category_groups'] as $group) {
				$groupObj = $this->categoryGroupRepo->findOneBy(['id' => $group, 'target' => 3]);
				if (!$groupObj) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
				}
				$workflow->addCategoryGroup($groupObj);
			}
		}

		$this->em->persist($workflow);
		$this->em->persist($workflowParams);
		$this->em->flush();

		return new ApiResponse(data: Factory::workflowDtoInstance($workflow));
	}

	public function processUpdate(Request $request, array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$userCategoryGroups = $user->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}

		/** @var WFWorkflow $workflow */
		$workflow = $this->wfRepo->find($params['id']);
		if (!$workflow) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
		}

		$haveGroup = false;
		foreach ($userCategoryGroups as $userCategoryGroup) {
			if (in_array($userCategoryGroup, $workflow->getCategoryGroupsCodes())) {
				$haveGroup = true;
			}
		}

		if (!$haveGroup) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		$workflowParams = $workflow->getParameters();
		if (!empty($params['name'])) {
			$existingWf = $this->wfRepo->findOneBy(['name' => $params['name']]);
			if ($existingWf && $existingWf->getId() !== $workflow->getId()) {
				return new ErrorResponse(
					Response::HTTP_CONFLICT,
					ApiError::CODE_DUPLICATE_NAME,
					ApiError::$descriptions[ApiError::CODE_DUPLICATE_NAME]
				);
			}
			$workflow->setName(strip_tags($params['name']));
		}

		if (!empty($params['notification_type'])) {
			if (!in_array($params['notification_type'], Constants::getWfNotificationTypes())) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'notification_type');
			}
			$workflowParams->setNotificationType($params['notification_type']);
		}

		if (!empty($params['run_automatically'])) {
			$workflow->setRunAutomatically($params['run_automatically']);
		}

		if (!empty($params['run_pattern'])) {
			$runPattern = $params['run_pattern'];
			try {
				new CronExpression($runPattern);
				$workflow->setRunPattern($runPattern);
			} catch (\Throwable) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'run_pattern');
			}
		}

		if (!empty($params['workflow_type'])) {
			if (isset($params['workflow_type']) && !in_array($params['workflow_type'], Constants::getWfTypes())) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'workflow_type');
			}
			$workflow->setType(strval($params['workflow_type']));
		}

		if (!empty($params['params'])) {
			$workflowParams->setParams($params['params']);
		}

		if (!empty($params['filters'])) {
			$dbParams = $workflowParams->getParams();
			$dbParams['filters'] = $params['filters'];
			$workflowParams->setParams($dbParams);
		}

		if (!empty($params['description'])) {
			$workflow->setDescription(strip_tags($params['description']));
		}

		if (!empty($params['notification_target'])) {
			$workflowParams->setNotificationTarget($params['notification_target']);
		}

		if (!empty($params['category_groups'])) {
			$groups = $workflow->getCategoryGroups();

			foreach ($groups as $group) {
				$workflow->removeCategoryGroup($group);
			}

			foreach ($params['category_groups'] as $group) {
				$groupObj = $this->categoryGroupRepo->findOneBy(['id' => $group, 'target' => 3]);
				if (!$groupObj) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
				}
				$workflow->addCategoryGroup($groupObj);
			}
		}
		$this->em->persist($workflow);
		$this->em->persist($workflowParams);
		$this->em->flush();

		return new ApiResponse(data: Factory::workflowDtoInstance($workflow));
	}

	public function processDelete(string $id): ApiResponse
	{
		$workflow = $this->wfRepo->find($id);
		if (!$workflow) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
		}
		$this->em->remove($workflow);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processRun(Request $request, array $params): ApiResponse
	{
		$data = $this->workflowRunProcess($params, $request);

		if ($data instanceof ErrorResponse) {
			return $data;
		}

		$this->redisClients->redisMainDB->rpush(
			RedisClients::SESSION_KEY_AWAITING_WORKFLOWS,
			json_encode(['id' => $data['monitorId']])
		);

		return new ApiResponse(code: Response::HTTP_CREATED, data: ['id' => $data['monitorId']]);
	}

	/**
	 * @throws ExceptionInterface
	 */
	public function processDispatch(Request $request, array $params): ApiResponse
	{
		$data = $this->workflowRunProcess($params, $request);

		if ($data instanceof ErrorResponse) {
			return $data;
		}

		$message = new WorkflowRunMessage($data['name'], $data['monitorId']);
		$this->bus->dispatch($message);

		return new ApiResponse(code: Response::HTTP_CREATED, data: ['id' => $data['monitorId']]);
	}

	public function processMonitorHistory(Request $request, array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$internalUser = $params['internal_user_id'] ?? $user->getId();
		$userObj = $this->userRepo->find($internalUser);

		if (!$userObj) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$userCategoryGroups = $userObj->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}
		$status = $params['status'] ?? [];
		$search = $params['search'] ?? null;

		if (count($status)) {
			foreach ($status as $stat) {
				if (!in_array($stat, [
					AVWorkflowMonitor::STATUS_FAILED,
					AVWorkflowMonitor::STATUS_PENDING,
					AVWorkflowMonitor::STATUS_RUNNING,
					AVWorkflowMonitor::STATUS_FINISHED,
				])) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
				}
			}
		}
		$totalRows = $this->wfmRepo->getCountRows();
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$dataQuery = array_merge([
			'start' => $paginationDto->from,
			'limit' => $paginationDto->perPage,
			'status' => $status,
			'internal_user_id' => $internalUser,
			'category_groups' => $userCategoryGroups,
			'search' => $search,
		], $params);
		$result = $this->wfmRepo->getList($dataQuery);

		$response = new DefaultPaginationResponse(
			[
				'entities' => $this->prepareMonitorListResponse($result),
			]
		);

		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function retrieveMonitor(Request $request, string $id): ApiResponse
	{
		/** @var AVWorkflowMonitor $workflowMonitor */
		$workflowMonitor = $this->wfmRepo->find($id);
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (!$workflowMonitor) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow_monitor');
		}

		$userCategoryGroups = $user->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}
		$haveGroup = false;
		foreach ($userCategoryGroups as $userCategoryGroup) {
			if (in_array($userCategoryGroup, $workflowMonitor->getWorkflow()->getCategoryGroupsCodes())) {
				$haveGroup = true;
			}
		}

		if (!$haveGroup) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		return new WorkflowMonitorResponse(
			[
				'entities' => [$workflowMonitor],
			]
		);
	}

	private function prepareMonitorListResponse(array $objectList): array
	{
		$result = [];
		/** @var AVWorkflowMonitor $entity */
		foreach ($objectList as $entity) {
			$createdByDto = null;
			$createdBy = $entity->getCreatedBy();
			if ($createdBy) {
				$createdByDto = new GenericPersonDto(
					$createdBy->getId(),
					$createdBy->getFirstName(),
					$createdBy->getLastName(),
					$createdBy->getEmail(),
				);
			}
			$result[] = (new WorkflowMonitorDto(
				$entity->getId(),
				$createdByDto,
				$entity->getWorkflow()?->getName(),
				$entity->getStatus(),
				$entity->getWorkflow()?->getType(),
				$entity->getOrderedAt()?->format(DateConstant::GLOBAL_FORMAT),
				$entity->getStartedAt()?->format(DateConstant::GLOBAL_FORMAT),
				$entity->getFinishedAt()?->format(DateConstant::GLOBAL_FORMAT),
				null
			));
		}

		return $result;
	}

	private function prepareWfListResponse(array $objectList): array
	{
		$result = [];
		/** @var WFWorkflow $entity */
		foreach ($objectList as $entity) {
			$categoryGroupList = [];
			foreach ($entity->getCategoryGroups()?->getValues() as $catGroup) {
				$categoryGroupList[] = [
					'id' => $catGroup->getId(),
					'name' => $catGroup->getName(),
					'code' => $catGroup->getCode(),
				];
			}
			$result[] = (new WorkflowDto(
				$entity->getId(),
				$entity->getName(),
				$entity->getDescription(),
				$entity->getType(),
				$entity->getParameters()->getNotificationType(),
				$entity->getParameters()?->getNotificationTarget(),
				$entity->getParameters()?->getParams(),
				$entity->isRunAutomatically(),
				$entity->getLastRunAt()?->format(DateConstant::GLOBAL_FORMAT),
				$entity->getRunPattern(),
				$categoryGroupList,
			));
		}

		return $result;
	}

	private function workflowRunProcess(array $params, Request $request): mixed
	{
		/** @var WFWorkflow $workflow */
		$workflow = $this->wfRepo->find($params['id']);
		unset($params['id']);

		if (!$workflow) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
		}

		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$userCategoryGroups = $user->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}
		$haveGroup = false;
		foreach ($userCategoryGroups as $userCategoryGroup) {
			if (in_array($userCategoryGroup, $workflow->getCategoryGroupsCodes())) {
				$haveGroup = true;
			}
		}

		if (!$haveGroup) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}
		$workflowMonitor = new AVWorkflowMonitor();
		$workflowMonitor
			->setCreatedBy($user)
			->setWorkflow($workflow);

		if (!empty($params['params'])) {
			$currentParams = $workflow->getParameters();
			$workflowMonitor->setDetails(['params' => array_merge($currentParams->getParams(), $params['params'])]);
		}
		$this->em->persist($workflowMonitor);
		$this->em->flush();

		return [
			'name' => $workflow->getName(),
			'monitorId' => $workflowMonitor->getId(),
		];
	}
}
