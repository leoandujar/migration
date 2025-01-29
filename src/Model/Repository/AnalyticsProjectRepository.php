<?php

namespace App\Model\Repository;

use App\Model\Entity\Activity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;

class AnalyticsProjectRepository extends EntityRepository
{
	public const int MAX_FETCH_ATTEMPTS = 20;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(AnalyticsProject::class);
		parent::__construct($entityManager, $class);
	}

	public function findByExternalIdAndLang(
		int $externalId,
		string $language,
		string $country = null,
		string $script = null
	): array {
		$return = $this->createQueryBuilder('a')
			->leftJoin('a.targetLanguage', 'l')
			->andWhere('a.externalId = :externalId')
			->andWhere('l.languageCode = :lCode')
			->setParameter('externalId', $externalId)
			->setParameter('lCode', $language);

		if (null === $country) {
			$return = $return->andWhere('l.countryCode IS NULL');
		} else {
			$return = $return->andWhere('l.countryCode = :lCountry')
				->setParameter('lCountry', $country);
		}

		if (null === $script) {
			$return = $return->andWhere('l.script IS NULL');
		} else {
			$return = $return->andWhere('l.script = :lScript')
				->setParameter('lScript', $script);
		}

		return $return->getQuery()
			->getResult();
	}

	
	public function findByExternalIdAndExternalIso1Lang(int $externalId, string $language): ?AnalyticsProject
	{
		return $this->createQueryBuilder('a')
			->leftJoin('a.targetLanguage', 'l')
			->andWhere('a.externalId = :externalId')
			->andWhere('l.externalIso1 = :lCode')
			->setParameter('externalId', $externalId)
			->setParameter('lCode', $language)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findByExternalIdAndExternalIso2Lang(int $externalId, string $language): ?AnalyticsProject
	{
		return $this->createQueryBuilder('a')
			->leftJoin('a.targetLanguage', 'l')
			->andWhere('a.externalId = :externalId')
			->andWhere('l.externalIso2 = :lCode')
			->setParameter('externalId', $externalId)
			->setParameter('lCode', $language)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findForProcessing(?int $limit = 200): array
	{
		return $this->createQueryBuilder('a')
			->where('a.ignored = :ignored')
			->andWhere('a.processingStatus = :status')
			->orderBy('a.externalId', 'ASC')
			->setMaxResults($limit)
			->setParameters(new ArrayCollection([
				new Parameter('ignored', false),
				new Parameter('status', AnalyticsProject::CREATED),
			]))
			->getQuery()
			->getResult();
	}

	public function findForLqaProcessing(?int $limit = 200): array
	{
		return $this->createQueryBuilder('a')
			->where('a.lqaAllowed = :lqaAllowed')
			->andWhere('a.lqaProcessed = :lqaProcessed')
			->andWhere('a.processingStatus >= :processingStatus')
			->setParameter('lqaAllowed', true)
			->setParameter('lqaProcessed', false)
			->setParameter('processingStatus', AnalyticsProject::METRICS_PROCESSED)
			->orderBy('a.externalId', 'ASC')
			->setMaxResults($limit)
			->getQuery()
			->getResult();
	}

	public function findProjectForLqaProcessing(?int $id): array
	{
		return $this->createQueryBuilder('a')
			->where('a.lqaAllowed = :lqaAllowed')
			->andWhere('a.externalId = :externalId')
			->andWhere('a.processingStatus >= :processingStatus')
			->setParameter('lqaAllowed', true)
			->setParameter('externalId', $id)
			->setParameter('processingStatus', AnalyticsProject::METRICS_PROCESSED)
			->orderBy('a.externalId', 'ASC')
			->setMaxResults(100)
			->getQuery()
			->getResult();
	}

	public function findForJobsProcessing(?int $limit = 200): array
	{
		$q = $this
			->createQueryBuilder('a')
			->where('a.ignored = :ignored')
			->andWhere('a.processingStatus = :processingStatus')
			->andWhere('a.status = :status')
			->orderBy('a.externalId', 'ASC')
			->setMaxResults($limit)
			->setParameters(new ArrayCollection([
				new Parameter('ignored', false),
				new Parameter('processingStatus', AnalyticsProject::LINKED),
				new Parameter('status', AnalyticsProject::S_FINISHED),
			]));

		return $q->getQuery()->getResult();
	}

	public function getIdsForMetricsProcessing(?int $limit = 200): array
	{
		$q = $this->createQueryBuilder('a');
		$q->select('a.externalId')
			->distinct()
			->andWhere('a.ignored = :ignored')
			->andWhere('a.status = :status')
			->andWhere(
				$q->expr()->orX(
					$q->expr()->eq('a.processingStatus', ':processingStatusProcesed'),
					$q->expr()->eq('a.processingStatus', ':processingStatusLinked')
				)
			)
			->andWhere('a.fetchAttempts < :maxFetchAttempts')
			->setParameters(new ArrayCollection([
				new Parameter('ignored', false),
				new Parameter('processingStatusProcesed', AnalyticsProject::JOBS_PROCESSED),
				new Parameter('processingStatusLinked', AnalyticsProject::LINKED),
				new Parameter('maxFetchAttempts', static::MAX_FETCH_ATTEMPTS),
				new Parameter('status', AnalyticsProject::S_FINISHED),
				]))
			->orderBy('a.externalId', 'ASC')
			->setMaxResults($limit);

		return $q->getQuery()->getArrayResult();
	}

	public function getIdsForStatisticsProcessing(?int $limit = 200): array
	{
		return $this->createQueryBuilder('a')
			->select('a.externalId')
			->distinct()
			->andWhere('a.ignored = :ignored')
			->andWhere('a.status = :status')
			->andWhere('a.processingStatus = :processingStatus')
			->andWhere('a.fetchAttempts < :maxFetchAttempts')
			->setParameter('ignored', false)
			->setParameter('processingStatus', AnalyticsProject::METRICS_PROCESSED)
			->setParameter('maxFetchAttempts', static::MAX_FETCH_ATTEMPTS)
			->setParameter('status', AnalyticsProject::S_FINISHED)
			->orderBy('a.externalId', 'ASC')
			->setMaxResults($limit)
			->getQuery()
			->getArrayResult();
	}

	public function findForTableExtendedProcessing(?int $limit): array
	{
		$limit = $limit ?? 200;

		return $this->createQueryBuilder('a')
			->andWhere('a.ignored = :ignored')
			->andWhere('a.status = :status')
			->andWhere('a.editDistanceAllowed = :editDistanceAllowed')
			->andWhere('a.editDistanceStatus = :editDistanceStatus')
			->andWhere('a.processingStatus = :processingStatus')
			->andWhere('a.fetchAttempts < :maxFetchAttempts')
			->andWhere('a.activity = :activityActive')
			->setParameter('ignored', false)
			->setParameter('editDistanceAllowed', true)
			->setParameter('processingStatus', AnalyticsProject::STATISTICS_PROCESSED)
			->setParameter('maxFetchAttempts', static::MAX_FETCH_ATTEMPTS)
			->setParameter('status', AnalyticsProject::S_FINISHED)
			->setParameter('editDistanceStatus', AnalyticsProject::ED_NOT_STARTED)
			->setParameter('activityActive', AnalyticsProject::A_ACTIVE)
			->orderBy('a.externalId', 'ASC')
			->setMaxResults($limit)
			->getQuery()
			->getResult();
	}

	public function findByExternalId(int $externalId): ?array
	{
		return $this->findBy(['externalId' => $externalId]);
	}

	public function findOneByExternalIdAndLanguage(int $externalId, string $targetLanguage): ?AnalyticsProject
	{
		return $this->findOneBy(['externalId' => $externalId, 'targetLanguageCode' => $targetLanguage]);
	}

	public function findByProjectHumanId(string $projectHumanId): ?array
	{
		return $this->findBy(['projectHumanId' => $projectHumanId]);
	}

	public function findByLanguageCode(int $externalId, string $targetLanguageCode): ?AnalyticsProject
	{
		return $this->findOneBy(['externalId' => $externalId, 'targetLanguageCode' => $targetLanguageCode]);
	}

	public function findOneByJob(Activity $activity): ?AnalyticsProject
	{
		return $this->findOneBy(['activity' => $activity]);
	}

	public function refreshCounter(): mixed
	{
		$qb = $this->createQueryBuilder('ap');
		$qb->update()
			->set('ap.fetchAttempts', 0)
			->where('ap.fetchAttempts > 0');

		return $qb->getQuery()->execute();
	}

	public function getCountSearchAnalyticProject(array $params): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('ap');
		$qb
			->select('COUNT(ap.id)')
			->leftJoin('ap.job', 'act');

		$this->analyticProjectSearchFilter($params, $qb);

		return $qb->getQuery()->getSingleScalarResult();
	}

	public function getSearchAnalyticProject(array $params): mixed
	{
		$qb = $this->createQueryBuilder('ap');
		$qb->select(
			'ap',
			'act'
		)
			->leftJoin('ap.job', 'act')
			->setFirstResult($params['start'])
			->setMaxResults($params['perPage']);

		if (isset($params['sortBy']) && isset($params['sortOrder'])) {
			$qb->orderBy("ap.{$params['sortBy']}", $params['sortOrder']);
		}

		$this->analyticProjectSearchFilter($params, $qb);

		return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}

	private function analyticProjectSearchFilter(array $params, &$qb): void
	{
		if (!empty($params['projectId'])) {
			$qb
				->andWhere($qb->expr()->eq('ap.project', ':projectId'))
				->setParameter('projectId', $params['projectId']);
		}
		if (!empty($params['taskId'])) {
			$qb
				->andWhere($qb->expr()->eq('ap.task', ':taskId'))
				->setParameter('taskId', $params['taskId']);
		}
		if (!empty($params['activityId'])) {
			$qb
				->andWhere($qb->expr()->eq('ap.job', ':activityId'))
				->setParameter('activityId', $params['activityId']);
		}
		if (!empty($params['targetLanguageTag'])) {
			$qb
				->andWhere($qb->expr()->in('ap.targetLanguageTag', ':targetLanguageTag'))
				->setParameter('targetLanguageTag', $params['targetLanguageTag']);
		}
		if (!empty($params['targetLanguage'])) {
			$qb
				->andWhere($qb->expr()->in('ap.targetLanguage', ':targetLanguage'))
				->setParameter('targetLanguage', $params['targetLanguage']);
		}
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->eq('ap.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (!empty($params['status'])) {
			$qb
				->andWhere($qb->expr()->in('ap.status', ':status'))
				->setParameter('status', $params['status']);
		}
		if (!empty($params['processingStatus'])) {
			$qb
				->andWhere($qb->expr()->in('ap.processingStatus', ':processingStatus'))
				->setParameter('processingStatus', $params['processingStatus']);
		}
		if (null !== $params['ignored']) {
			$qb
				->andWhere($qb->expr()->eq('ap.ignored', ':ignored'))
				->setParameter('ignored', $params['ignored']);
		}
		if (null !== $params['lqaAllowed']) {
			$qb
				->andWhere($qb->expr()->eq('ap.lqaAllowed', ':lqaAllowed'))
				->setParameter('lqaAllowed', $params['lqaAllowed']);
		}
		if (null !== $params['lqaProcessed']) {
			$qb
				->andWhere($qb->expr()->eq('ap.lqaProcessed', ':lqaProcessed'))
				->setParameter('lqaProcessed', $params['lqaProcessed']);
		}
		if (!empty($params['search'])) {
			$qb
				->andWhere(
					$qb->expr()->orX(
						$qb->expr()->like('LOWER(ap.name)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(ap.status)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}
	}
}
