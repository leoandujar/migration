<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Model\Entity\Action;
use App\Model\Entity\Permission;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use App\Model\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Model\Repository\PermissionsRepository;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\ContactPersonRepository;

class PermissionsHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private PermissionsRepository $permissionRepository;
	private InternalUserRepository $internalUserRepository;
	private ContactPersonRepository $contactPersonRepository;

	public function __construct(
		EntityManagerInterface $em,
		InternalUserRepository $internalUserRepository,
		ContactPersonRepository $contactPersonRepository,
		PermissionsRepository $userRepository
	) {
		$this->em = $em;
		$this->permissionRepository = $userRepository;
		$this->internalUserRepository = $internalUserRepository;
		$this->contactPersonRepository = $contactPersonRepository;
	}

	public function processListByRole(array $params): ApiResponse
	{
		$role = $this->em->getRepository(Role::class)->find($params['id']);

		if (!$role) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
		}

		$result = $this->permissionRepository->getActionsListByUserOrRoles([$role->getCode()]);

		return new ApiResponse(data: $result);
	}

	public function processUpdateByRole(array $params): ApiResponse
	{
		$role = $this->em->getRepository(Role::class)->find($params['id']);

		if (!$role) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
		}

		$originalPermissions = $role->getPermissions();

		foreach ($originalPermissions as $permission) {
			if (!in_array($permission->getAction()->getCode(), $params['permissions'])) {
				$role->removePermission($permission);
			}
		}

		foreach ($params['permissions'] as $permission) {
			$actionObj = $this->em->getRepository(Action::class)->findOneBy(['code' => $permission]);

			if (!$actionObj) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'action');
			}

			$permissionObj = $this->permissionRepository->findOneBy(['action' => $actionObj->getId(), 'role' => $role->getId()]);

			if (!$permissionObj) {
				$permissionObj = new Permission();
				$permissionObj
				->setAction($actionObj)
				->setActive(true)
				->setRole($role);
				$role->addPermission($permissionObj);
				$this->em->persist($permissionObj);
			}
		}

		$this->em->persist($role);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processListByUser(array $params): ApiResponse
	{
		$type = $params['type'];

		if (Permission::TARGET_ADMIN_PORTAL === $type) {
			$user = $this->internalUserRepository->find($params['id']);
			$target = Action::TARGET_ADMIN_PORTAL;
		}

		if (Permission::TARGET_CLIENT_PORTAL === $type) {
			$user = $this->contactPersonRepository->find($params['id']);
			$target = Action::TARGET_CLIENT_PORTAL;
		}

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$result = $this->permissionRepository->getActionsByUserOrRoles($user->getId(), $type);

		return new ApiResponse(data: $result);
	}

	public function processUpdateByUser(array $params): ApiResponse
	{
		$type = $params['type'];

		$actionObj = $this->em->getRepository(Action::class)->findOneBy(['code' => strtoupper($params['code'])]);

		if (!$actionObj) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'action');
		}

		if (Permission::TARGET_ADMIN_PORTAL === $type) {
			$user = $this->internalUserRepository->find($params['id']);
			$permissionObj = $this->permissionRepository->findOneBy(['action' => $actionObj->getId(), 'internalUser' => $params['id']]);
		}

		if (Permission::TARGET_CLIENT_PORTAL === $type) {
			$user = $this->contactPersonRepository->find($params['id']);
			$permissionObj = $this->permissionRepository->findOneBy(['action' => $actionObj->getId(), 'cpUser' => $params['id']]);
		}

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (!$permissionObj) {
			$permissionObj = new Permission();
			$permissionObj->setAction($actionObj);
			$user->addPermission($permissionObj);
		}
		$permissionObj->setActive(boolval($params['permission']));
		if (Permission::TARGET_ADMIN_PORTAL === $type) {
			$permissionObj->setInternalUser($user);
		}
		if (Permission::TARGET_CLIENT_PORTAL === $type) {
			$permissionObj->setCpUser($user);
		}
		$this->em->persist($permissionObj);
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
