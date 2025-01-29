<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\Action;
use App\Model\Entity\InternalUser;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\PermissionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Apis\Shared\Handlers\SecurityHandler as BaseSecurityHandler;

class AccountHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private SecurityHandler $securityHandler;
	private UserPasswordHasherInterface $encoder;
	private InternalUserRepository $userRepository;
	private PermissionsRepository $permissionsRepository;
	private BaseSecurityHandler $baseSecurityHandler;

	public function __construct(
		EntityManagerInterface $em,
		UserPasswordHasherInterface $encoder,
		InternalUserRepository $userRepository,
		SecurityHandler $securityHandler,
		PermissionsRepository $permissionsRepository,
		BaseSecurityHandler $baseSecurityHandler
	) {
		$this->em = $em;
		$this->encoder = $encoder;
		$this->userRepository = $userRepository;
		$this->securityHandler = $securityHandler;
		$this->permissionsRepository = $permissionsRepository;
		$this->baseSecurityHandler = $baseSecurityHandler;
	}

	public function processUpdate(array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (isset($params['email'])) {
			$existingUser = $this->userRepository->findOneBy(['email' => $params['email']]);
			if ($existingUser && $existingUser->getId() !== $user->getId()) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_DUPLICATE_EMAIL, ApiError::$descriptions[ApiError::CODE_DUPLICATE_EMAIL], 'email');
			}
			$user->setEmail($params['email']);
		}
		if (isset($params['mobile'])) {
			$user->setMobile($params['mobile']);
		}
		if (isset($params['department'])) {
			$user->setDepartment($params['department']);
		}
		if (isset($params['position'])) {
			$user->setPosition($params['position']);
		}
		if (isset($params['first_name'])) {
			$user->setFirstName($params['first_name']);
		}
		if (isset($params['last_name'])) {
			$user->setLastName($params['last_name']);
		}

		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(data: Factory::internalUserDtoInstance($user));
	}

	public function processChangePassword(array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$oldPassword = $params['old_password'];
		$newPassword = $params['new_password'];

		if (!$this->encoder->isPasswordValid($user, $oldPassword)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'password');
		}

		$user->setPassword($this->encoder->hashPassword($user, $newPassword));
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processGetPermissions(): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$actionList = [];
		$actions = $this->em->getRepository(Action::class)->findAll();
		/** @var Action $action */
		foreach ($actions as $action) {
			$actionList[$action->getCode()] = false;
		}

		$userAllowedActions = $this->permissionsRepository->getActionsByUserOrRoles($user->getId());

		foreach ($actionList as $action => $value) {
			if (isset($userAllowedActions[$action])) {
				$actionList[$action] = $userAllowedActions[$action];
			}
		}

		$result['permissions'] = $actionList;

		return new ApiResponse(data: $result);
	}

	public function processRetrieve(): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$abilities = $this->baseSecurityHandler->getAbilities($user->getRoles());

		return new ApiResponse(data: Factory::internalUserDtoInstance($user, $abilities));
	}
}
