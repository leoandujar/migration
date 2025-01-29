<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Apis\Shared\Util\UtilsService;
use App\Model\Entity\AnalyticsProject;
use App\Model\Repository\AnalyticsProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class AnalyticProjectHandler
{
	private UtilsService $utilsSrv;
	private EntityManagerInterface $em;
	private AnalyticsProjectRepository $analyticsProjectRepo;

	public function __construct(
		UtilsService $utilsSrv,
		AnalyticsProjectRepository $analyticsProjectRepo,
		EntityManagerInterface $em
	) {
		$this->em = $em;
		$this->analyticsProjectRepo = $analyticsProjectRepo;
		$this->utilsSrv = $utilsSrv;
	}

	public function processRetrieve(string $id): ApiResponse
	{
		$analytic = $this->analyticsProjectRepo->find($id);
		if (!$analytic) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'analytic');
		}

		return new ApiResponse(data: Factory::analyticProjectDtoInstance($analytic));
	}

	public function processSearch(array $params): ApiResponse
	{
		$this->utilsSrv->arrayKeysToCamel($params);
		$totalRows = $this->analyticsProjectRepo->getCountSearchAnalyticProject($params);
		$paginationDto = new PaginationDto($params['page'], $params['perPage'], $totalRows, $params['sortOrder'], $params['sortBy']);
		$params['start'] = $paginationDto->from;
		$sqlResponse = $this->analyticsProjectRepo->getSearchAnalyticProject($params);

		$result = [];
		foreach ($sqlResponse as $analytic) {
			$result[] = Factory::analyticProjectDtoInstance($analytic);
		}

		$response = new DefaultPaginationResponse(data: $result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processFieldList(): ApiResponse
	{
		$reflectionExtractor = new ReflectionExtractor();
		$propertyInfo = new PropertyInfoExtractor(
			[
				$reflectionExtractor,
			],
		);
		$class = AnalyticsProject::class;

		return new ApiResponse(data: $propertyInfo->getProperties($class));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$id = $params['id'];
		unset($params['id']);
		$analytics = $this->analyticsProjectRepo->findByExternalId($id);
		if (!$analytics) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'analytics');
		}
		$reflectionExtractor = new ReflectionExtractor();
		$propertyInfo = new PropertyInfoExtractor(
			[
				$reflectionExtractor,
			],
		);
		$class = AnalyticsProject::class;
		$properties = $propertyInfo->getProperties($class);
		$propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
			->enableMagicSet()
			->getPropertyAccessor();

		foreach ($params as $field => $value) {
			if (!in_array($field, $properties)) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'field =>'.$field);
			}
			foreach ($analytics as $analytic) {
				$propertyAccessor->setValue($analytic, $field, $value);
				$this->em->persist($analytic);
			}
		}

		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
