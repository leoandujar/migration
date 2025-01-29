<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Message\FlowRunMessage;
use App\Model\Entity\AvFlow;
use App\Model\Entity\AvFlowMonitor;
use App\Model\Entity\AvFlowAction;
use App\Model\Entity\InternalUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Finder\Finder;

class FlowHandler
{
	private MessageBusInterface $bus;
	private EntityManagerInterface $em;
	private SecurityHandler $securityHandler;

	public function __construct(
		MessageBusInterface $bus,
		EntityManagerInterface $em,
		SecurityHandler $securityHandler,
	) {
		$this->bus = $bus;
		$this->em = $em;
		$this->securityHandler = $securityHandler;
	}

	public function processRun(string $flowId, array $params): ApiResponse
	{
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}
			$userCategoryGroups = $user->getCategoryGroups() ?? [];

			if (!count($userCategoryGroups)) {
				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
			}

			$flowObj = $this->em->getRepository(AvFlow::class)->find($flowId);

			if (!$flowObj) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'Flow');
			}

			$flowMonitor = new AvFlowMonitor();
			$flowMonitor->setFlow($flowObj);
			$flowMonitor->setRequestedBy($user);

			$this->em->persist($flowMonitor);
			$this->em->flush();

			$monitorId = $flowMonitor->getId();

			$monitorObj = $this->em->getRepository(AvFlowMonitor::class)->find($monitorId);
			$monitorObj->setResult(['errors' => [], 'successful' => []]);
			$monitorObj->setDetails($params['inputs'] ?? []);
			$this->em->persist($monitorObj);
			$this->em->flush();

			$this->bus->dispatch(new FlowRunMessage($flowId, $monitorId));
		} catch (\Throwable $thr) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return new ApiResponse(data: ['id' => $monitorId], code: Response::HTTP_CREATED);
	}

	public function processCreate(array $data): ApiResponse
	{
		$currentDateTime = new \DateTime();
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}

			$flowObj = (new AvFlow())
				->setName($data['name'])
				->setDescription($data['description'])
				->setRunAutomatically($data['run_automatically'])
				->setCreatedAt($currentDateTime)
				->setUpdatedAt($currentDateTime)
				->setRunPattern($data['run_pattern'])
				->setParameters($data['params'] ?? []);

			$this->em->persist($flowObj);
			$this->em->flush();
		} catch (\Throwable) {
			return new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return new ApiResponse(data: Factory::FlowDtoInstance($flowObj), code: Response::HTTP_CREATED);
	}

	public function processDelete(string $id): ApiResponse
	{
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}

			$flowObj = $this->em->getRepository(AvFlow::class)->find($id);

			if (!$flowObj) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
			}

			if ($startAction = $flowObj->getStartAction()) {
				$flowObj->setStartAction(null);
				$this->em->remove($startAction);
			}

			foreach ($flowObj->getActions() as $action) {
				$this->em->remove($action);
			}
			$flowObj->clearActions();

			foreach ($flowObj->getMonitors() as $monitor) {
				$this->em->remove($monitor);
			}
			$flowObj->clearMonitors();

			$this->em->remove($flowObj);
			$this->em->flush();
		} catch (\Throwable) {
			return new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processList(mixed $params): ApiResponse
	{
		$response = null;
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}
			$userCategoryGroups = $user->getCategoryGroups() ?? [];

			if (!count($userCategoryGroups)) {
				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
			}

			$flowRepo = $this->em->getRepository(AvFlow::class);

			$search = $params['search'] ?? null;
			$runAutomatically = $params['run_automatically'] ?? null;
			$runPattern = $params['run_pattern'] ?? null;
			$totalRows = $flowRepo->getCountRows($params);

			$paginationDto = new PaginationDto($params['page'] ?? 1, $params['per_page'] ?? 1, $totalRows, $params['sort_order'] ?? '', $params['sort_by'] ?? '');
			$dataQuery = array_merge([
				'start' => $paginationDto->from,
				'run_automatically' => $runAutomatically,
				'run_pattern' => $runPattern,
				'category_groups' => $userCategoryGroups,
				'search' => $search,
			], $params);

			$result = $flowRepo->getWithFilters($dataQuery);
			$entities = [];

			/** @var AvFlow $entity */
			foreach ($result as $entity) {
				$entities[] = Factory::FlowDtoInstance($entity);
			}

			$response = new DefaultPaginationResponse(
				[
					'entities' => $entities,
				]
			);

			$response->setPaginationDto($paginationDto);

		} catch (\Throwable) {
			return new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return $response;
	}

	public function processRetrieve(string $id): ApiResponse
	{
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}

			$flow = $this->em->getRepository(AvFlow::class)->find($id);

			if (!$flow) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'Flow');
			}
		} catch (\Throwable) {
			return new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return new ApiResponse(data: Factory::FlowDtoInstance($flow), code: Response::HTTP_OK);
	}

	public function processActions(): ApiResponse
	{
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}

			$steps = $this->getFlowSteps();
		} catch (\Throwable $thr) {
			return new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return new ApiResponse(data: $steps, code: Response::HTTP_OK);
	}

	public function processUpdate(string $id, array $data): ApiResponse
	{
		try {
			/** @var InternalUser $user */
			$user = $this->securityHandler->getCurrentUser();

			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}

			$flowObj = $this->em->getRepository(AvFlow::class)->find($id);

			if (!$flowObj) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
			}

			$flowObj->setName($data['name'] ?? $flowObj->getName())
				->setDescription($data['description'] ?? $flowObj->getDescription())
				->setRunAutomatically($data['run_automatically'] ?? $flowObj->getRunAutomatically())
				->setUpdatedAt(new \DateTime())
				->setParameters($data['params'] ?? $flowObj->getParameters() ?? []);

			if (isset($data['actions']) && is_array($data['actions'])) {
				$flowObj->clearActions();
				$actionAux = [];

				foreach ($data['actions'] as $actionData) {
					if (!isset($actionData['slug'])) {
						throw new BadRequestHttpException('Action slug is required');
					}
					$actionObj = new AvFlowAction();
					$actionParts = explode('\\', $actionData['action']);
					$actionObj->setName($actionData['name'])
						->setDescription($actionData['description'])
						->setAction($actionData['action'])
						->setCategory($actionParts[0])
						->setSlug($actionData['slug'])
						->setInputs($actionData['inputs'] ?? [])
						->setInputsOnStart(null !== $actionData['inputsOnStart'] ? [] : null)
						->setOutputs([]);

					$actionAux[$actionData['slug']] = $actionObj;
				}

				foreach ($data['actions'] as $actionData) {
					if (isset($actionData['next']) && $actionData['next'] && isset($actionAux[$actionData['next']])) {
						$actionAux[$actionData['slug']]->setNext($actionAux[$actionData['next']]);
					} else {
						$actionAux[$actionData['slug']]->setNext(null);
					}
				}

				if ($data['startAction'] && isset($actionAux[$data['startAction']])) {
					$flowObj->setStartAction($actionAux[$data['startAction']]);
					$this->em->persist($flowObj);
				}

				foreach ($actionAux as $actionObj) {
					$flowObj->addAction($actionObj);
				}
			}

			$this->em->persist($flowObj);
			$this->em->flush();
		} catch (\Throwable $thr) {
			return new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR], 'Flow');
		}

		return new ApiResponse(data: Factory::FlowDtoInstance($flowObj), code: Response::HTTP_OK);
	}

	private function getFlowSteps(): array
	{
		$finder = new Finder();
		$directory = dirname(__DIR__, 4).'/src/Flow/Actions/';
		$finder->files()->in($directory)->name('*.php');
		$classNames = [];

		foreach ($finder as $file) {
			$relativePath = $file->getRelativePathname();
			$className = str_replace(
				['/', '.php'],
				['\\', ''],
				$relativePath
			);
			if ('Action' === $className) {
				continue;
			}
			$rawName = 'App\\Flow\\Actions\\'.$className;
			try {
				$description = $rawName::ACTION_DESCRIPTION;
				$inputs = $rawName::ACTION_INPUTS;
				$outputs = $rawName::ACTION_OUTPUTS;
			} catch (\Throwable $thr) {
				$description = null;
				$inputs = [];
			}
			$flowStep = new AvFlowAction();
			$flowStep->setName($className)
				->setId('')
				->setDescription($description)
				->setAction(explode('\\', $className)[1])
				->setInputs((is_array($inputs)) ? $inputs : null)
				->setNext(null)
				->setCategory(explode('\\', $className)[0])
				->setOutputs((is_array($outputs)) ? $outputs : null);

			$classNames[] = Factory::flowActionDtoInstance($flowStep);
		}

		return $classNames;
	}

	public function retrieveMonitor(string $id): ApiResponse
	{
		/** @var AvFlowMonitor $flowMonitor */
		$flowMonitor = $this->em->getRepository(AvFlowMonitor::class)->find($id);
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (!$flowMonitor) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow_monitor');
		}

		$userCategoryGroups = $user->getCategoryGroups() ?? [];

		if (!count($userCategoryGroups)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_EMPTY_GROUPS, ApiError::$descriptions[ApiError::CODE_EMPTY_GROUPS]);
		}

		return new ApiResponse(data: Factory::flowMonitorDtoInstance($flowMonitor), code: Response::HTTP_OK);
	}

	public function processMonitorHistory(Request $request, array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$internalUser = $params['internal_user_id'] ?? $user->getId();
		$userObj = $this->em->getRepository(InternalUser::class)->find($internalUser);

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
					AvFlowMonitor::STATUS_FAILED,
					AvFlowMonitor::STATUS_RUNNING,
					AvFlowMonitor::STATUS_REQUESTED,
					AvFlowMonitor::STATUS_FINISHED,
				])) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
				}
			}
		}

		$totalRows = $this->em->getRepository(AvFlowMonitor::class)->getCountRows($params);
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);

		$dataQuery = array_merge([
			'start' => $paginationDto->from,
			'limit' => $paginationDto->perPage,
			'status' => $status,
			'internal_user_id' => $internalUser,
			'category_groups' => $userCategoryGroups,
			'search' => $search,
		], $params);
		$result = $this->em->getRepository(AvFlowMonitor::class)->getList($dataQuery);

		$data = [];
		/** @var AvFlowMonitor $entity */
		foreach ($result as $entity) {
			$data[] = Factory::flowMonitorDtoInstance($entity);
		}

		$response = new DefaultPaginationResponse(
			[
				'entities' => $data,
			]
		);

		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function generateSlug(string $actionName): string
	{
		$slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($actionName));
		$slug = trim($slug, '-');

		return sprintf('%s-%03d', $slug, random_int(0, 999));
	}
}
