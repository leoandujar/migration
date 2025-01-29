<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\Role;
use App\Apis\Shared\Http\Error\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RoleHandler extends BaseHandler
{
	private EntityManagerInterface $em;
	private TokenStorageInterface $tokenStorage;
	private ContactPersonRepository $contactPersonRepository;
	private RequestStack $requestStack;

	public function __construct(
		EntityManagerInterface $em,
		TokenStorageInterface $tokenStorage,
		RequestStack $requestStack,
		ContactPersonRepository $contactPersonRepository
	) {
		parent::__construct($requestStack, $em);
		$this->em                      = $em;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->tokenStorage            = $tokenStorage;
		$this->requestStack = $requestStack;
	}

	public function processGetList(): ApiResponse
	{
		$roles = $this->em->getRepository(Role::class)->findBy(['target' => Role::TARGET_CLIENT_PORTAL]);
		$result = [];
		foreach ($roles as $role) {
			$result[] = Factory::roleDtoInstance($role);
		}

		return new ApiResponse(data: $result);
	}

	public function processAssingRoleToUser(array $params): ApiResponse
	{
		$id       = $params['id'];
		$roleList = $params['roles'];

		$user = $this->getCurrentUser();
		$newUser = $this->contactPersonRepository->find($id);

		if (!$newUser) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if ($user->getId() === $newUser->getId()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_SELF_ASSIGN_ERROR, ApiError::$descriptions[ApiError::CODE_SELF_ASSIGN_ERROR]);
		}

		if ($user->getCustomersPerson()->getCustomer()->getId() !== $newUser->getCustomersPerson()->getCustomer()->getId()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_CUSTOMERS_DOES_NOT_MATCH, ApiError::$descriptions[ApiError::CODE_CUSTOMERS_DOES_NOT_MATCH]);
		}

		foreach ($roleList as $role) {
			$role = $this->em->getRepository(Role::class)->findOneBy(['code' => strtoupper($role)]);
			if (!$role) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
			}
		}

		if (!in_array(Role::ROLE_CP_BASE, $roleList)) {
			$roleList[] = Role::ROLE_CP_BASE;
		}

		$newUser->setRoles($roleList);
		$this->em->persist($newUser);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
