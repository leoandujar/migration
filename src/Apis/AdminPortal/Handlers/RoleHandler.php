<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\Role;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class RoleHandler
{
	use UserResolver;

	private EntityManagerInterface $em;

	public function __construct(
		EntityManagerInterface $em
	) {
		$this->em = $em;
	}

	public function processGetList(int $target = null): ApiResponse
	{
		if (null !== $target) {
			$target = intval($target);
			if (Role::TARGET_ADMIN_PORTAL !== $target && Role::TARGET_CLIENT_PORTAL !== $target) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE]);
			}
			$roles = $this->em->getRepository(Role::class)->findBy(['target' => intval($target)], ['name' => 'ASC']);
		} else {
			$roles = $this->em->getRepository(Role::class)->findAll();
		}

		$result = [];
		foreach ($roles as $role) {
			$result[] = Factory::roleDtoInstance($role);
		}

		return new ApiResponse(data: $result);
	}

	public function processCreate(array $params): ApiResponse
	{
		$role = $this->em->getRepository(Role::class)->findOneBy(['code' => strtoupper($params['code']), 'target' => $params['target']]);

		if ($role) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}

		if (empty($params['abilities'])) {
			$params['abilities'] = [];
		}

		$role = new Role();
		$role
			->setName($params['name'])
			->setCode(strtoupper($params['code']))
			->setTarget($params['target'])
			->setAbilities($params['abilities']);
		$this->em->persist($role);
		$this->em->flush();

		return new ApiResponse(data: Factory::roleDtoInstance($role));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$role = $this->em->getRepository(Role::class)->find($params['id']);

		if (!$role) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
		}
		if (!empty($params['name'])) {
			$role->setName($params['name']);
		}
		if (!empty($params['target']) && (Role::TARGET_ADMIN_PORTAL === $params['target'] || Role::TARGET_CLIENT_PORTAL === $params['target'])) {
			$role->setTarget($params['target']);
		}
		if (!empty($params['abilities'])) {
			$role->setAbilities($params['abilities']);
		}
		if (!empty($params['code'])) {
			$role->setCode(strtoupper($params['code']));
		}

		$this->em->persist($role);
		$this->em->flush();

		return new ApiResponse(data: Factory::roleDtoInstance($role));
	}

	public function processDelete(array $params): ApiResponse
	{
		$role = $this->em->getRepository(Role::class)->find($params['id']);

		if (!$role) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
		}

		$this->em->remove($role);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
