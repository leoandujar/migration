<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVChart;
use App\Model\Repository\AVChartRepository;
use App\Model\Repository\AVReportTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ReportChartHandler
{
	private EntityManagerInterface $em;
	private AVChartRepository $chartRepo;
	private AVReportTypeRepository $reportTypeRepo;

	public function __construct(
		EntityManagerInterface $em,
		AVChartRepository $chartRepo,
		AVReportTypeRepository $reportTypeRepo,
	) {
		$this->em = $em;
		$this->chartRepo = $chartRepo;
		$this->reportTypeRepo = $reportTypeRepo;
	}

	public function processList(array $params): ApiResponse
	{
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $this->chartRepo->getCountRows(), $params['sort_order'], $params['sort_by']);
		$dataQuery = array_merge([
			'start' => $paginationDto->from,
			'limit' => $paginationDto->perPage,
			'search' => $params['search'] ?? null,
			'slug' => $params['slug'] ?? null,
			'name' => $params['name'] ?? null,
			'active' => $params['active'] ?? null,
			'report_type_id' => $params['report_type_id'] ?? null,
		], $params);
		$sqlResponse = $this->chartRepo->getSearch($dataQuery);
		$result = [];
		foreach ($sqlResponse as $parameter) {
			$result[] = Factory::avChartDtoInstance($parameter);
		}

		$response = new DefaultPaginationResponse(data: $result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function retrieve(string $id): ApiResponse
	{
		$chart = $this->chartRepo->find($id);

		if (!$chart) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'chart');
		}

		return new ApiResponse(data: Factory::avChartDtoInstance($chart));
	}

	public function processCreate(array $params): ApiResponse
	{
		$chart = $this->chartRepo->findBy(['slug' => $params['slug']]);
		if ($chart) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}

		$reportType = $this->reportTypeRepo->find($params['report_type_id']);
		if (!$reportType) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_type');
		}

		$chart = new AVChart();
		$chart
			->setName(strip_tags($params['name']))
			->setSlug(strip_tags($params['slug']))
			->setCategory(strip_tags($params['category']))
			->setType(intval($params['type']))
			->setActive(boolval($params['active']))
			->setReportType($reportType);

		if (isset($params['description'])) {
			$chart->setDescription(strip_tags($params['description']));
		}

		if (isset($params['size'])) {
			$chart->setSize(intval($params['size']));
		}

		if (isset($params['options'])) {
			$chart->setOptions($params['options']);
		}

		if (isset($params['return_y'])) {
			$chart->setReturnY(strip_tags($params['return_y']));
		}

		$this->em->persist($chart);
		$this->em->flush();

		return new ApiResponse(data: Factory::avChartDtoInstance($chart));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$chart = $this->chartRepo->find($params['id']);
		if (!$chart) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'chart');
		}

		if (!empty($params['slug'])) {
			$existingChart = $this->chartRepo->findOneBy(['slug' => $params['slug']]);
			if ($existingChart && $existingChart->getId() !== $chart->getId()) {
				return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_DUPLICATE_NAME, ApiError::$descriptions[ApiError::CODE_DUPLICATE_NAME]);
			}
			$chart->setSlug(strip_tags($params['slug']));
		}

		if (!empty($params['report_type_id'])) {
			$reportType = $this->reportTypeRepo->find($params['report_type_id']);
			if (!$reportType) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_type');
			}
			$chart->setReportType($reportType);
		}

		if (!empty($params['name'])) {
			$chart->setName(strip_tags($params['name']));
		}

		if (!empty($params['category'])) {
			$chart->setCategory(strip_tags($params['category']));
		}

		if (!empty($params['options'])) {
			$chart->setOptions($params['options']);
		}

		if (isset($params['active'])) {
			$chart->setActive(boolval($params['active']));
		}

		if (isset($params['description'])) {
			$chart->setDescription(strip_tags($params['description']));
		}

		if (isset($params['size'])) {
			$chart->setSize(intval($params['size']));
		}

		if (isset($params['type'])) {
			$chart->setType(intval($params['type']));
		}

		if (isset($params['return_y'])) {
			$chart->setReturnY(strip_tags($params['return_y']));
		}

		$this->em->persist($chart);
		$this->em->flush();

		return new ApiResponse(data: Factory::avChartDtoInstance($chart));
	}

	public function processDelete(string $id): ApiResponse
	{
		$chart = $this->chartRepo->find($id);
		if (!$chart) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'chart');
		}
		$this->em->remove($chart);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
