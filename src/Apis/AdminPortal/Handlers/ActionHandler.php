<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\Action;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class ActionHandler
{
	use UserResolver;

	private EntityManagerInterface $em;

	public function __construct(
		EntityManagerInterface $em,
	) {
		$this->em = $em;
	}

	public function processGetList(int $target = null): ApiResponse
	{
		if (null !== $target) {
			if (Action::TARGET_ADMIN_PORTAL !== $target && Action::TARGET_CLIENT_PORTAL !== $target) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'target');
			}
			$actions = $this->em->getRepository(Action::class)->findBy(['target' => $target], ['code' => 'ASC']);
		} else {
			$actions = $this->em->getRepository(Action::class)->findAll();
		}

		$result = [];
		foreach ($actions as $action) {
			$result[] = Factory::actionDtoInstance($action);
		}

		return new ApiResponse(data: $result);
	}

	public function processCreate(array $params): ApiResponse
	{
		$action = $this->em->getRepository(Action::class)->findOneBy(['code' => strtoupper($params['code']), 'target' => $params['target']]);

		if ($action) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}
		$action = new Action();
		$action
			->setName($params['name'])
			->setCode(strtoupper($params['code']))
			->setTarget($params['target']);
		$this->em->persist($action);
		$this->em->flush();

		return new ApiResponse(data: Factory::actionDtoInstance($action));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$action = $this->em->getRepository(Action::class)->find($params['id']);

		if (!$action) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'action');
		}
		if (!empty($params['name'])) {
			$action->setName($params['name']);
		}
		if (!empty($params['code'])) {
			$action->setCode(strtoupper($params['code']));
		}
		if (!empty($params['target']) && (Action::TARGET_ADMIN_PORTAL === $params['target'] || Action::TARGET_CLIENT_PORTAL === $params['target'])) {
			$action->setTarget($params['target']);
		}
		$this->em->persist($action);
		$this->em->flush();

		return new ApiResponse(data: Factory::actionDtoInstance($action));
	}

	public function processDelete(array $params): ApiResponse
	{
		$action = $this->em->getRepository(Action::class)->find($params['id']);

		if (!$action) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'action');
		}

		$this->em->remove($action);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
