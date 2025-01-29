<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\DTO\APTemplateDto;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\APTemplate;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\APTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class TemplateHandler
{
	private EntityManagerInterface $em;
	private APTemplateRepository $templateRepo;
	private InternalUserRepository $internalPersonRepo;

	public function __construct(
		EntityManagerInterface $em,
		APTemplateRepository $templateRepo,
		InternalUserRepository $internalPersonRepo
	) {
		$this->em = $em;
		$this->templateRepo = $templateRepo;
		$this->internalPersonRepo = $internalPersonRepo;
	}

	public function processGetList(array $params): ApiResponse
	{
		$targetEntity = $params['target_entity'] ?? null;

		$id = null;
		if (!empty($params['internal_user_id'])) {
			$id = $params['internal_user_id'];
		}

		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $this->templateRepo->getCountSearch(), $params['sort_order'], $params['sort_by']);
		$sqlResponse = $this->templateRepo->getList($id, $targetEntity, $paginationDto->from, $params['per_page'], $params['sort_by'], $params['sort_order']);
		$response = new DefaultPaginationResponse(
			[
				'entities' => $this->prepareSearchResponse($sqlResponse),
			]
		);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processCreate(array $params): ApiResponse
	{
		$user = $this->internalPersonRepo->find($params['internal_user_id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$exists = $this->templateRepo->findOneBy(['name' => $params['name'], 'internalUser' => $user->getId()]);
		if ($exists) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ENTITY_EXISTS, ApiError::$descriptions[ApiError::CODE_ENTITY_EXISTS]);
		}

		$template = new APTemplate();
		$template
			->setName(strip_tags($params['name']))
			->setTargetEntity($params['target_entity'])
			->setData($params['data'])
			->setInternalUser($user);
		$this->em->persist($template);
		$this->em->flush();

		return new ApiResponse(data: Factory::templateDtoInstance($template));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$template = $this->templateRepo->find($params['id']);
		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}
		if (!empty($params['internal_user_id'])) {
			$user = $this->internalPersonRepo->find($params['internal_user_id']);
			if (!$user) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}
			$template->setInternalUser($user);
		}

		if (!empty($params['name']) && !empty($params['internal_user_id'])) {
			$exists = $this->templateRepo->findOneBy(['name' => $params['name'], 'internalUser' => $user->getId()]);
			if ($exists) {
				return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ENTITY_EXISTS, ApiError::$descriptions[ApiError::CODE_ENTITY_EXISTS]);
			}
		}

		if (!empty($params['name'])) {
			$template->setName(strip_tags($params['name']));
		}
		if (!empty($params['target_entity'])) {
			$template->setTargetEntity($params['target_entity']);
		}
		if (!empty($params['data'])) {
			$template->setData($params['data']);
		}
		$this->em->persist($template);
		$this->em->flush();

		return new ApiResponse(data: Factory::templateDtoInstance($template));
	}

	public function processDelete(int $id): ApiResponse
	{
		$template = $this->templateRepo->find($id);
		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		$this->em->remove($template);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function prepareSearchResponse(array $objectList): array
	{
		$result = [];
		foreach ($objectList as $template) {
			$result[] = (new APTemplateDto(
				$template->getId(),
				$template->getName(),
				$template->getTargetEntity(),
				$template->getData()
			));
		}

		return $result;
	}
}
