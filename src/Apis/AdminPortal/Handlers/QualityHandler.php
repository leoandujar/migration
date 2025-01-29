<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\QualityIssue;
use App\Model\Entity\QualityReport;
use App\Apis\Shared\Http\Error\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\ActivityRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\QualityReportRepository;
use App\Model\Repository\QualityCategoryRepository;

class QualityHandler
{
	private EntityManagerInterface $em;
	private ActivityRepository $activityRepository;
	private QualityReportRepository $qualityReportRepository;
	private QualityCategoryRepository $qualityCategoryRepository;

	public function __construct(
		QualityReportRepository $qualityReportRepository,
		QualityCategoryRepository $qualityCategoryRepository,
		ActivityRepository $activityRepository,
		EntityManagerInterface $em
	) {
		$this->em = $em;
		$this->qualityReportRepository = $qualityReportRepository;
		$this->qualityCategoryRepository = $qualityCategoryRepository;
		$this->activityRepository = $activityRepository;
	}

	public function processSearchCategories(array $params): ApiResponse
	{
		$type = $params['type'] ?? null;
		$qualityCategories = $this->qualityCategoryRepository->getParentCategories(strtoupper($type));

		return new ApiResponse(data: $qualityCategories);
	}

	public function processGetChildCategories(array $params): ApiResponse
	{
		$qualityCategories = $this->qualityCategoryRepository->getChildCategories($params['id']);

		return new ApiResponse(data: $qualityCategories);
	}

	public function processSearchReports(array $params): ApiResponse
	{
		$status = $params['status'] ?? [];
		$type = $params['type'] ?? null;
		$search = $params['search'] ?? null;

		$totalRows = $this->qualityReportRepository->getCountReports([
			'status' => $status,
			'search' => $search,
			'type' => $type,
		]);
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$dataQuery = array_merge([
			'start' => $paginationDto->from,
			'limit' => $paginationDto->perPage,
			'status' => $status,
			'search' => $search,
			'type' => $type,
		], $params);
		$result = $this->qualityReportRepository->findReports($dataQuery);

		$response = new DefaultPaginationResponse($result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processGetReport(array $params): ApiResponse
	{
		$qualityReport = $this->qualityReportRepository->find($params['id']);

		return new ApiResponse(data: Factory::qualityReportDtoInstance($qualityReport, true));
	}

	public function processGetReportByActivity(array $params): ApiResponse
	{
		$activityId = $params['activity_id'];
		$type = $params['type'];
		$activity = $this->activityRepository->find($activityId);
		if (!$activity) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'activity');
		}
		$qualityReport = $this->qualityReportRepository->findOneBy(['activity' => $activity->getId(), 'type' => $type]);
		if (!$qualityReport) {
			return new ApiResponse(data: Factory::activityDtoInstance($activity));
		}

		return new ApiResponse(data: Factory::qualityReportDtoInstance($qualityReport, true));
	}

	public function processDeleteReport(array $params): ApiResponse
	{
		$qualityReportId = $params['quality_report_id'];
		$qualityReport = $this->qualityReportRepository->find($qualityReportId);
		if (!$qualityReport) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quality_report');
		}
		$this->em->remove($qualityReport);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processCreateReport(array $params): ApiResponse
	{
		$activityId = $params['activity_id'];
		$activity = $this->activityRepository->find($activityId);
		if (!$activity) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'activity');
		}

		$date = new \DateTime();
		$qualityReport = new QualityReport();
		$qualityReport
			->setProoferName($params['proofer_name'])
			->setPageCount($params['page_count'])
			->setFormat(implode(',', (array) $params['format']))
			->setStatus($params['status'])
			->setType($params['type'] ?? QualityReport::CATEGORY_DQA)
			->setExcellent(false)
			->setActivity($activity)
			->setCreatedDate($date)
			->setLastModificationDate($date);
		$this->em->persist($qualityReport);
		$this->em->flush();

		return new ApiResponse(data: Factory::qualityReportDtoInstance($qualityReport, true));
	}

	public function processUpdateReportStatus(array $params): ApiResponse
	{
		$qualityReportId = $params['quality_report_id'];
		$qualityReport = $this->qualityReportRepository->find($qualityReportId);
		if (!$qualityReport) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quality_report');
		}
		$date = new \DateTime();
		$qualityReport
			->setStatus($params['status'])
			->setLastModificationDate($date);
		$this->em->persist($qualityReport);
		$this->em->flush();

		return new ApiResponse(data: Factory::qualityReportDtoInstance($qualityReport, false));
	}

	public function processCalculateReportStatus(array $params): ApiResponse
	{
		$qualityReportId = $params['quality_report_id'];
		$excellent = $params['excellent'] ?? false;
		$qualityReport = $this->qualityReportRepository->find($qualityReportId);
		if (!$qualityReport) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quality_report');
		}
		$date = new \DateTime();

		$minorMultiplier = $qualityReport->getMinorMultiplier();
		$majorMultiplier = $qualityReport->getMajorMultiplier();
		$criticalMultiplier = $qualityReport->getCriticalMultiplier();

		$qualityIssues = $qualityReport->getQualityIssues();
		$totalIssues = count($qualityIssues);
		$totalSum = 0;

		/** @var QualityIssue $qualityIssue */
		foreach ($qualityIssues as $qualityIssue) {
			if ($qualityIssue->getQualityCategory()->getWeight() > 0) {
				$totalMinor = $qualityIssue->getMinor() * $minorMultiplier;
				$totalMajor = $qualityIssue->getMajor() * $majorMultiplier;
				$totalCritical = $qualityIssue->getCritical() * $criticalMultiplier;

				$totalCategory = $totalMinor + $totalMajor + $totalCritical;
				$totalSum = $totalSum + ($totalCategory * $qualityIssue->getQualityCategory()->getWeight());
			}
		}
		$pageCount = $qualityReport->getPageCount() ?? 1;
		$totalIssuesTotal = ($totalIssues > 0 ? $totalIssues : 1) * 4;
		match ($qualityReport->getType()) {
			QualityReport::CATEGORY_DQA => $totalScore = round(1 - ($totalSum / (100 * $pageCount)), 4),
			QualityReport::CATEGORY_PQA, QualityReport::CATEGORY_PME => $totalScore = round($totalSum / $totalIssuesTotal, 4),
		};

		$qualityReport
			->setStatus(QualityReport::STATUS_FINISHED)
			->setScore($totalScore)
			->setLastModificationDate($date)
			->setExcellent($excellent);
		$this->em->persist($qualityReport);
		$this->em->flush();

		return new ApiResponse(data: Factory::qualityReportDtoInstance($qualityReport, false));
	}

	public function processAttachIssues(array $params): ApiResponse
	{
		$qualityReportId = $params['quality_report_id'];
		$qualityIssuesNew = $params['quality_issues'];
		/** @var QualityReport $qualityReport */
		$qualityReport = $this->qualityReportRepository->find($qualityReportId);
		if (!$qualityReport) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quality_report');
		}
		$qualityIssuesOld = $qualityReport->getQualityIssues();
		$qualityIssuesOld->clear();
		$this->em->persist($qualityReport);
		$ids = [];
		foreach ($qualityIssuesNew as $qualityIssueNew) {
			$id = (int) $qualityIssueNew['id'];
			if (in_array($id, $ids)) {
				continue;
			}
			$ids[] = $id;
			$qualityCategory = $this->qualityCategoryRepository->find($id);
			if (!$qualityCategory || !$qualityCategory->getParentCategory()) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quality_category');
			}
			$qualityIssue = new QualityIssue();
			$qualityIssue
				->setMinor($qualityIssueNew['minor'] ?? 0)
				->setMajor($qualityIssueNew['major'] ?? 0)
				->setCritical($qualityIssueNew['critical'] ?? 0)
				->setComment($qualityIssueNew['comment'])
				->setQualityCategory($qualityCategory);

			$qualityReport->addQualityIssue($qualityIssue);
		}
		$this->em->persist($qualityReport);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
