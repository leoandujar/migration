<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\InternalUser;
use App\Model\Entity\Role;
use App\Model\Repository\CategoryGroupRepository;
use App\Model\Repository\CustomerRepository;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private CustomerRepository $customerRepo;
	private UserPasswordHasherInterface $encoder;
	private InternalUserRepository $userRepository;
	private CategoryGroupRepository $categoryGroupRepo;
	private UserRepository $xtrfUserRepository;

	public function __construct(
		EntityManagerInterface $em,
		CustomerRepository $customerRepo,
		UserPasswordHasherInterface $encoder,
		InternalUserRepository $userRepository,
		CategoryGroupRepository $categoryGroupRepo,
		UserRepository $xtrfUserRepository
	) {
		$this->em = $em;
		$this->encoder = $encoder;
		$this->customerRepo = $customerRepo;
		$this->userRepository = $userRepository;
		$this->categoryGroupRepo = $categoryGroupRepo;
		$this->xtrfUserRepository = $xtrfUserRepository;
	}

	public function processGetList(array $params): ApiResponse
	{
		if (!empty($params['roles'])) {
			$users = $this->userRepository->findByRoles($params);
		} else {
			$users = $this->userRepository->findBy(['type' => InternalUser::TYPE_INTERNAL]);
		}
		$result = [];
		foreach ($users as $user) {
			$result[] = Factory::internalUserDtoInstance($user);
		}

		return new ApiResponse(data: $result);
	}

	public function processGetOptions(array $params): ApiResponse
	{
		$result = $this->xtrfUserRepository->findInternal($params);

		return new ApiResponse(data: $result);
	}

	public function processCreate(array $params): ApiResponse
	{
		$user = $this->userRepository->findOneBy(['username' => $params['username']]);

		if ($user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}
		foreach ($params['roles'] as $role) {
			if (!is_string($role)) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
			}
			$role = $this->em->getRepository(Role::class)->findOneBy(['code' => $role]);
			if (!$role) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
			}
		}
		if (!empty($params['categoryGroups'])) {
			foreach ($params['category_groups'] as $categoryGroup) {
				if (!is_string($categoryGroup)) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
				}
				$categoryGroup = $this->categoryGroupRepo->findOneBy(['code' => $categoryGroup]);
				if (!$categoryGroup) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
				}
			}
		}
		$user = new InternalUser();
		$user
			->setUsername($params['username'])
			->setFirstName($params['first_name'])
			->setLastName($params['last_name'])
			->setEmail($params['email'])
			->setRoles($params['roles'])
			->setLastLoginDate(new \DateTime());
		if (isset($params['mobile'])) {
			$user->setMobile($params['mobile']);
		}
		if (isset($params['type'])) {
			$user->setType($params['type']);
		}
		if (isset($params['position'])) {
			$user->setPosition($params['position']);
		}
		if (isset($params['department'])) {
			$user->setDepartment($params['department']);
		}
		if (isset($params['status'])) {
			$user->setStatus($params['status']);
		}
		if (isset($params['category_groups'])) {
			$user->setCategoryGroups($params['category_groups']);
		}
		$user->setPassword($this->encoder->hashPassword($user, $params['password'] ?? md5(random_bytes(10))));

		$xtrfUser = $this->xtrfUserRepository->findOneBy(['login' => $params['email']]);
		if ($xtrfUser) {
			$user->setXtrfUser($xtrfUser);
		}
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(data: Factory::internalUserDtoInstance($user));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$user = $this->userRepository->find($params['id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (!empty($params['first_name'])) {
			$user->setFirstName(trim($params['first_name']));
		}

		if (!empty($params['last_name'])) {
			$user->setLastName(trim($params['last_name']));
		}

		if (!empty($params['username'])) {
			$user->setUsername(trim($params['username']));
		}

		if (!empty($params['department'])) {
			$user->setUsername(trim($params['department']));
		}

		if (!empty($params['position'])) {
			$user->setUsername(trim($params['position']));
		}

		if (!empty($params['mobile'])) {
			$user->setUsername(trim($params['mobile']));
		}

		if (!empty($params['email'])) {
			$userByEmail = $this->userRepository->findOneBy(['email' => $params['id']]);
			if ($userByEmail) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_DUPLICATE_EMAIL, ApiError::$descriptions[ApiError::CODE_DUPLICATE_EMAIL]);
			}
			$user->setEmail($params['email']);
		}

		if (!empty($params['roles'])) {
			foreach ($params['roles'] as $role) {
				$role = $this->em->getRepository(Role::class)->findOneBy(['code' => $role]);
				if (!$role) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
				}
			}
			$user->setRoles($params['roles']);
		}

		if (!empty($params['category_groups'])) {
			foreach ($params['category_groups'] as $categoryGroup) {
				$categoryGroup = $this->categoryGroupRepo->findOneBy(['code' => $categoryGroup]);
				if (!$categoryGroup) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
				}
			}
			$user->setCategoryGroups($params['category_groups']);
		}

		if (!empty($params['status'])) {
			if (InternalUser::STATUS_ACTIVE !== (int) $params['status'] && InternalUser::STATUS_INACTIVE !== (int) $params['status']) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
			}
			$user->setStatus($params['status']);
		}

		if (!empty($params['all_customers_access']) && null !== $params['all_customers_access']) {
			$user->setCpLoginGodMode(boolval($params['all_customers_access']));
		}

		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(data: Factory::internalUserDtoInstance($user));
	}

	public function processChangePassword(array $params): ApiResponse
	{
		$user = $this->userRepository->find($params['id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$newPassword = $params['password'];

		$user->setPassword($this->encoder->hashPassword($user, $newPassword));
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDelete(array $params): ApiResponse
	{
		$user = $this->userRepository->find($params['id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$this->em->remove($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processAssignCpLoginCustomer(array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->userRepository->find($params['id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		foreach ($params['customer_ids'] as $customerId) {
			$customer = $this->customerRepo->find($customerId);
			if (!$customer) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
			}
		}

		$user->setCpLoginCustomers($params['customer_ids']);
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
