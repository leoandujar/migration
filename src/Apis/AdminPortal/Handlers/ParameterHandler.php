<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVParameter;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\AVParameterRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class ParameterHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private AVParameterRepository $avParamRepo;

	public function __construct(
		EntityManagerInterface $em,
		AVParameterRepository $avParamRepo
	) {
		$this->em = $em;
		$this->avParamRepo = $avParamRepo;
	}

	public function processGetList(array $params): ApiResponse
	{
		$totalRows = $this->avParamRepo->getCountSearch($params);
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$sqlResponse = $this->avParamRepo->getList($params['name'] ?? null, $params['scope'] ?? null, $paginationDto->from, $params['per_page'], $params['sort_by'], $params['sort_order']);

		$result = [];
		foreach ($sqlResponse as $parameter) {
			$result[] = Factory::parameterDtoInstance($parameter);
		}

		$response = new DefaultPaginationResponse(data: $result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processCreate(array $params): ApiResponse
	{
		$parameter = $this->avParamRepo->findOneBy(['scope' => strtolower($params['scope']), 'name' => strtolower($params['name'])]);

		if ($parameter) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}
		$parameter = new AVParameter();
		$parameter
			->setName(strtolower($params['name']))
			->setScope(strtolower($params['scope']))
			->setValue($params['value']);
		$this->em->persist($parameter);
		$this->em->flush();

		return new ApiResponse(data: Factory::parameterDtoInstance($parameter));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$parameter = $this->avParamRepo->find($params['id']);

		if (!$parameter) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'parameter');
		}

		if (!empty($params['name']) && !empty($params['scope'])) {
			$parameterExist = $this->avParamRepo->findOneBy(['scope' => strtolower($params['scope']), 'name' => strtolower($params['name'])]);

			if ($parameterExist && $parameterExist->getId() !== $parameter->getId()) {
				return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
			}
		}

		if (!empty($params['name'])) {
			$parameter->setName(htmlentities($params['name']));
		}

		if (!empty($params['scope'])) {
			$parameter->setScope(htmlentities($params['scope']));
		}

		if (!empty($params['value'])) {
			$parameter->setValue(htmlentities($params['value']));
		}
		$this->em->persist($parameter);
		$this->em->flush();

		return new ApiResponse(data: Factory::parameterDtoInstance($parameter));
	}

	public function processDelete(string $id): ApiResponse
	{
		$parameter = $this->avParamRepo->find($id);

		if (!$parameter) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'parameter');
		}

		$this->em->remove($parameter);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
