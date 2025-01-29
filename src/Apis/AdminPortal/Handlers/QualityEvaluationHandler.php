<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\InternalUser;
use App\Model\Entity\APQualityEvaluationRecord;
use App\Model\Entity\APQualityEvaluation;
use App\Apis\Shared\Http\Error\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\APQualityEvaluationRepository;
use App\Model\Repository\QualityCategoryRepository;
use App\Model\Repository\InternalUserRepository;

class QualityEvaluationHandler
{
	private EntityManagerInterface $em;
	private APQualityEvaluationRepository $evaluationRepository;
	private QualityCategoryRepository $qualityCategoryRepository;
	private InternalUserRepository $internalUserRepository;
	private SecurityHandler $securityHandler;

	public function __construct(
		APQualityEvaluationRepository $qualityEvaluationRepository,
		QualityCategoryRepository $qualityCategoryRepository,
		InternalUserRepository $internalUserRepository,
		EntityManagerInterface $em,
		SecurityHandler $securityHandler
	) {
		$this->em = $em;
		$this->evaluationRepository = $qualityEvaluationRepository;
		$this->qualityCategoryRepository = $qualityCategoryRepository;
		$this->internalUserRepository = $internalUserRepository;
		$this->securityHandler = $securityHandler;
	}

	public function processList(array $params): ApiResponse
	{
		$status = $params['status'] ?? [];
		$type = $params['type'] ?? null;
		$search = $params['search'] ?? null;

		$totalRows = $this->evaluationRepository->totalRows([
			'status' => $status,
			'type' => $type,
			'search' => $search,
		]);
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$filters = [
			'start' => $paginationDto->from,
			'limit' => $paginationDto->perPage,
			'status' => $status,
			'search' => $search,
			'type' => $type,
		];
		$sqlResult = $this->evaluationRepository->list($filters);

		$result = [];
		foreach ($sqlResult as $item) {
			$result[] = Factory::qualityEvaluationDtoInstance($item, false);
		}

		$response = new DefaultPaginationResponse(data: $result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processRetrieve(string $id): ApiResponse
	{
		$evaluation = $this->evaluationRepository->find($id);

		$result = Factory::qualityEvaluationDtoInstance($evaluation);

		return new ApiResponse(data: $result);
	}

	public function processDelete(array $params): ApiResponse
	{
		$evaluationId = $params['id'];
		$evaluation = $this->evaluationRepository->find($evaluationId);
		if (!$evaluation) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'evaluation');
		}
		$this->em->remove($evaluation);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processCreate(array $params): ApiResponse
	{
		$date = new \DateTime();
		/** @var InternalUser $user */
		$evaluator = $this->securityHandler->getCurrentUser();
		$evaluatee = $this->internalUserRepository->find($params['evaluatee_id']);
		if (!$evaluatee) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'person');
		}
		$evaluation = new APQualityEvaluation();
		$evaluation
			->setEvaluator($evaluator)
			->setEvaluatee($evaluatee)
			->setStatus(APQualityEvaluation::STATUS_FINISHED)
			->setType($params['type'] ?? APQualityEvaluation::TYPE_EPC)
			->setCreatedAt($date)
			->setExcellent($params['excellent'] ?? false)
			->setComment($params['comment'] ?? null);

		$records = $params['records'];
		$evaluationWithRecords = $this->processRecords($evaluation, $records);
		$evaluationWithScore = $this->processScore($evaluationWithRecords);

		$this->em->persist($evaluationWithScore);
		$this->em->flush();

		$result = Factory::qualityEvaluationDtoInstance($evaluation);

		return new ApiResponse(data: $result);
	}

	public function processScore(APQualityEvaluation $evaluation): APQualityEvaluation
	{
		$records = $evaluation->getRecords();
		$totalRecords = count($records);

		if (0 === $totalRecords) {
			return $evaluation;
		}

		$values = $records
			->map(function (APQualityEvaluationRecord $record) {
				return $record->getValue();
			})
			->getValues();

		$valueSum = array_sum($values);
		$score = round($valueSum / $totalRecords, 2);

		$evaluation
			->setScore($score);

		return $evaluation;
	}

	public function processRecords(APQualityEvaluation $evaluation, array $records): APQualityEvaluation
	{
		$recordsOld = $evaluation->getRecords();
		$recordsOld->clear();
		$ids = [];
		foreach ($records as $recordNew) {
			$id = (int) $recordNew['id'];
			if (in_array($id, $ids)) {
				continue;
			}
			$ids[] = $id;
			$qualityCategory = $this->qualityCategoryRepository->find($id);
			if (!$qualityCategory || !$qualityCategory->getParentCategory()) {
				continue;
			}
			$record = new APQualityEvaluationRecord();
			$record
				->setCategory($qualityCategory)
				->setValue($recordNew['value'] ?? 0)
				->setComment($recordNew['comment']);

			$this->em->persist($record);

			$evaluation->addRecord($record);
		}

		return $evaluation;
	}
}
