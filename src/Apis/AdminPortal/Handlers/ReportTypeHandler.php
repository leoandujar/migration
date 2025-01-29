<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVReportType;
use App\Model\Repository\AVReportTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ReportTypeHandler
{
	private EntityManagerInterface $em;
	private AVReportTypeRepository $reportTypeRepo;

	public function __construct(
		EntityManagerInterface $em,
		AVReportTypeRepository $reportTypeRepo
	) {
		$this->em = $em;
		$this->reportTypeRepo = $reportTypeRepo;
	}

	public function processList(array $params): ApiResponse
	{
		$reportTypeList = $this->reportTypeRepo->getSearch($params);

		return new ApiResponse(data: $reportTypeList);
	}

	public function retrieve(string $id): ApiResponse
	{
		$reportType = $this->reportTypeRepo->find($id);

		if (!$reportType) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_type');
		}

		return new ApiResponse(data: Factory::reportTypeDtoInstance($reportType));
	}

	public function processCreate(array $params): ApiResponse
	{
		$reportType = $this->reportTypeRepo->findByNameOrCode($params['name'], $params['code']);
		if ($reportType) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}
		$reportType = new AVReportType();

		if (!empty($params['children'])) {
			foreach ($params['children'] as $child) {
				$childReportType = $this->reportTypeRepo->findOneBy(['code' => $child]);
				if (!$childReportType) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'child');
				}
				$reportType->addChild($childReportType);
			}
		}

		if (!empty($params['parent'])) {
			$parentReportType = $this->reportTypeRepo->findOneBy(['code' => $params['parent']]);
			if (!$parentReportType) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'parent');
			}
			$reportType->setParent($parentReportType);
		}

		if (isset($params['function_name'])) {
			$reportType->setFunctionName(strip_tags($params['function_name']));
		}

		if (isset($params['description'])) {
			$reportType->setDescription(strip_tags($params['description']));
		}

		$reportType
			->setName(strip_tags($params['name']))
			->setCode(strip_tags($params['code']));
		$this->em->persist($reportType);
		$this->em->flush();

		return new ApiResponse(data: Factory::reportTypeDtoInstance($reportType));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$reportType = $this->reportTypeRepo->find($params['id']);
		if (!$reportType) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_type');
		}

		if (!empty($params['children'])) {
			foreach ($params['children'] as $child) {
				if ($child === $reportType->getId()) {
					return new ErrorResponse(
						Response::HTTP_BAD_REQUEST,
						ApiError::CODE_RECURSIVE_DEPENDENCY,
						ApiError::$descriptions[ApiError::CODE_RECURSIVE_DEPENDENCY]
					);
				}
				$childReportType = $this->reportTypeRepo->findOneBy(['code' => $child]);
				if (!$childReportType) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'child');
				}
				$reportType->addChild($childReportType);
			}
		}

		if (!empty($params['parent'])) {
			if ($params['parent'] === $reportType->getId()) {
				return new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_RECURSIVE_DEPENDENCY,
					ApiError::$descriptions[ApiError::CODE_RECURSIVE_DEPENDENCY]
				);
			}
			$parentReportType = $this->reportTypeRepo->findOneBy(['code' => $params['parent']]);
			if (!$parentReportType) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'parent');
			}
			$reportType->setParent($parentReportType);
		}

		if (!empty($params['name'])) {
			$existingReportType = $this->reportTypeRepo->findOneBy(['name' => $params['name']]);
			if ($existingReportType && $existingReportType->getId() !== $reportType->getId()) {
				return new ErrorResponse(
					Response::HTTP_CONFLICT,
					ApiError::CODE_DUPLICATE_NAME,
					ApiError::$descriptions[ApiError::CODE_DUPLICATE_NAME]
				);
			}
			$reportType->setName(strip_tags($params['name']));
		}

		if (!empty($params['code'])) {
			$existingReportType = $this->reportTypeRepo->findOneBy(['code' => $params['code']]);
			if ($existingReportType && $existingReportType->getId() !== $reportType->getId()) {
				return new ErrorResponse(
					Response::HTTP_CONFLICT,
					ApiError::CODE_DUPLICATE_CODE,
					ApiError::$descriptions[ApiError::CODE_DUPLICATE_CODE]
				);
			}
			$reportType->setCode(strip_tags($params['code']));
		}

		if (!empty($params['description'])) {
			$reportType->setDescription(strip_tags($params['description']));
		}

		if (!empty($params['function_name'])) {
			$reportType->setFunctionName(strip_tags($params['function_name']));
		}
		$this->em->persist($reportType);
		$this->em->flush();

		return new ApiResponse(data: Factory::reportTypeDtoInstance($reportType));
	}

	public function processDelete(string $id): ApiResponse
	{
		$reportType = $this->reportTypeRepo->find($id);
		if (!$reportType) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_type');
		}
		$this->em->remove($reportType);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
