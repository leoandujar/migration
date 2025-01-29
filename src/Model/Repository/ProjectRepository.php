<?php

namespace App\Model\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use App\Model\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProjectRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Project::class);
		parent::__construct($em, $class);
	}

	/**
	 * @return array|null
	 */
	public function getApList(string $partialName, int $limit, $customer, $status, bool $archived = false)
	{
		$q = $this->createQueryBuilder('p');
		$q->select(
			'p.id,
			p.idNumber as idNumber,
			p.customerProjectNumber as customerProjectNumber,
			p.name as name
			'
		)
			->where(
				$q->expr()->orX(
					$q->expr()->like('LOWER(p.idNumber)', 'LOWER(:partialName)'),
				)
			)
			->setMaxResults($limit);

		if (true === $archived) {
			$q->andWhere($q->expr()->isNotNull('p.archivedAt'));
		}

		if (!empty($customer)) {
			$q->andWhere($q->expr()->eq('p.customer', ':customer'));
			$q->setParameter('customer', $customer);
		}

		if (!empty($status)) {
			$q->andWhere($q->expr()->eq('p.status', ':status'));
			$q->setParameter('status', $status);
		}

		$q->setParameter(
			'partialName',
			"%$partialName%",
		);

		$q->orderBy('p.idNumber', 'ASC');

		return $q->getQuery()->getArrayResult();
	}

	public function getSearchProject(array $params): mixed
	{
		$qb = $this->createQueryBuilder('p');
		$qb->select('p')
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("p.{$params['sort_by']}", $params['sort_order']);
		}

		$this->projectSearchFilter($params, $qb);

		return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}

	public function getCountSearchProject(array $params): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('p');
		$qb->select('COUNT(p.id)');

		$this->projectSearchFilter($params, $qb);

		return $qb->getQuery()->getSingleScalarResult();
	}

	private function projectSearchFilter(array $params, &$qb): void
	{
		$initDate = '1970-01-01 00:00:00';
		$nowDate = (new \DateTime('now'))->format('Y-m-d H:i:s');
		if (!empty($params['status'])) {
			$qb
				->andWhere($qb->expr()->in('p.status', ':statuses'))
				->setParameter('statuses', $params['status']);
		}
		if (isset($params['survey_status']) && (true === $params['survey_status'] || false === $params['survey_status'])) {
			$qb
				->andWhere($qb->expr()->eq('p.surveySent', ':survey'))
				->setParameter('survey', $params['survey_status']);
		}
		if (!empty($params['customer_project_number'])) {
			$qb
				->andWhere($qb->expr()->eq('p.customerProjectNumber', ':refNumber'))
				->setParameter('refNumber', $params['customer_project_number']);
		}
		if (!empty($params['search']) || !empty($params['contact_person_id'])) {
			$qb
				->leftJoin('p.customerContactPerson', 'custp')
				->leftJoin('custp.contactPerson', 'contp');

			if (!empty($params['search'])) {
				$qb
					->andWhere(
						$qb->expr()->orX(
							$qb->expr()->like('LOWER(p.idNumber)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(p.customerProjectNumber)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(p.name)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(contp.name)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(contp.nameNormalized)', 'LOWER(:search)')
						)
					)
					->setParameter('search', "%{$params['search']}%");
			}
			if (!empty($params['contact_person_id'])) {
				$qb

					->andWhere(
						$qb->expr()->in('contp.id', ':contactPersonList')
					)
					->setParameter('contactPersonList', $params['contact_person_id']);
			}
		}
		if (!empty($params['requested_on'][0]) || !empty($params['requested_on'][1])) {
			$startDate = $params['requested_on'][0] ?? $initDate;
			$endDate = $params['requested_on'][1] ?? $nowDate;
			$endDate = (new \DateTime($endDate))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
			$qb
				->andWhere(
					$qb->expr()->between('p.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $startDate)
				->setParameter('endDate', $endDate);
		}
		if (!empty($params['deadline'][0]) || !empty($params['deadline'][1])) {
			$deadlineStartDate = $params['deadline'][0] ?? $initDate;
			$deadlineEndDate = $params['deadline'][1] ?? $nowDate;
			$deadlineEndDate = (new \DateTime($deadlineEndDate))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
			$qb
				->andWhere(
					$qb->expr()->between('p.deadline', ':deadlineStartDate', ':deadlineEndDate')
				)
				->setParameter('deadlineStartDate', $deadlineStartDate)
				->setParameter('deadlineEndDate', $deadlineEndDate);
		}
		if (!empty($params['customer_id'])) {
			$qb
				->andWhere(
					$qb->expr()->in('p.customer', ':customerId')
				)
				->setParameter('customerId', $params['customer_id']);
		}

		if (!empty($params['target_languages']) || !empty($params['source_languages'])) {
			$qb->innerJoin('p.languagesCombinations', 'langCombi');

			if (!empty($params['target_languages'])) {
				$qb->andWhere(
					$qb->expr()->in('langCombi.targetLanguage', ':targetLanguages')
				)
				->setParameter('targetLanguages', $params['target_languages']);
			}

			if (!empty($params['source_languages'])) {
				$qb->andWhere(
					$qb->expr()->in('langCombi.sourceLanguage', ':sourceLanguages')
				)
				->setParameter('sourceLanguages', $params['source_languages']);
			}
		}

		if (!empty($params['services'])) {
			$qb
				->andWhere($qb->expr()->in('p.service', ':services'))
				->setParameter('services', $params['services']);
		}
	}

	public function getEntries($date, $source, $entity, $deliveryDate): mixed
	{
		$qb = $this->createQueryBuilder('p');
		$qb->select('p.externalId as id')
			->addSelect(sprintf("'%s' as date", $date))
			->addSelect(sprintf("'%s' as source", $source))
			->addSelect(sprintf("'%s' as entity", $entity))
			->where($qb->expr()->orX(
				'p.status = :open',
				$qb->expr()->andX(
					'p.status = :close',
					'p.deliveryDate = :deliveryDate'
				)
			))
			->setParameters(new ArrayCollection([
				new Parameter('open', Project::STATUS_OPEN),
				new Parameter('close', Project::STATUS_CLOSED),
				new Parameter('deliveryDate', $deliveryDate),
			]));

		return $qb->getQuery()->getArrayResult();
	}

	public function getEntriesForDailyRefresh($date, $source, $entity, $createdOn): mixed
	{
		$qb = $this->createQueryBuilder('p');
		$qb->select('p.externalId as id')
			->addSelect(sprintf("'%s' as date", $date))
			->addSelect(sprintf("'%s' as source", $source))
			->addSelect(sprintf("'%s' as entity", $entity))
			->where('p.createdOn > :date')
			->setParameters(new ArrayCollection([
				new Parameter('date', $createdOn),
			]));

		return $qb->getQuery()->getArrayResult();
	}

	/**
	 * @return array|int|string
	 */
	public function getDeadlinePredictions(string $customerId)
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = '
		WITH date_diff AS (
			SELECT
				project_id,
				start_date,
				deadline,
				deadline - start_date AS total_diff
			FROM
				project
			where 
				customer_id = :customerId
				and
				deadline IS NOT NULL
				AND
				close_date IS NOT NULL
		),
		weekend_days as (
		SELECT
			project_id,
			COUNT(*) * 86400 AS weekend_seconds
		FROM
			date_diff,
			generate_series(
				start_date,
				deadline,
				\'1 day\'::interval
			) AS series(date)
		WHERE
			EXTRACT(DOW FROM series.date) IN (0, 6)
		GROUP BY
			project_id)
		SELECT
			AVG((EXTRACT(EPOCH FROM dd.total_diff) - COALESCE(wd.weekend_seconds, 0))/3600) AS hrs
		FROM
			date_diff dd
			LEFT JOIN
			weekend_days wd
		ON
			dd.project_id = wd.project_id
		';

		$resultSet = $conn->executeQuery($sql, ['customerId' => $customerId]);

		return $resultSet->fetchOne();
	}

	public function getProjects(string $customer, string $parameter, string $start, string $end): mixed
	{
		$qb = $this->createQueryBuilder('p');
		$qb->innerJoin('p.analyticsProjects', 'ap');
		$qb->where($qb->expr()->andX(
			$qb->expr()->between(sprintf('p.%s', $parameter), ':start', ':end'),
			$qb->expr()->eq('p.customer', ':customer')
		))->setParameters(new ArrayCollection([
			new Parameter('start', $start),
			new Parameter('end', $end),
			new Parameter('customer', $customer),
		]));

		return $qb->getQuery()->getResult();
	}

	public function getOpenedProjectsByEntity(?string $customerId = null, ?string $managerId = null, ?string $coordinatorId = null): mixed
	{
		if (empty($customerId) && empty($managerId) && empty($coordinatorId)) {
			return [];
		}
		$qb = $this->createQueryBuilder('p');
		$qb->where($qb->expr()->eq('p.status', ':statusOpened'))
			->setParameters(new ArrayCollection([
				new Parameter('statusOpened', Project::STATUS_OPEN),
			]));
		if ($customerId) {
			$qb->andWhere($qb->expr()->eq('p.customer', ':customerId'))
				->setParameter('customerId', $customerId);
		}
		if ($managerId) {
			$qb->andWhere($qb->expr()->eq('p.projectManager', ':managerId'))
				->setParameter('managerId', $managerId);
		}
		if ($coordinatorId) {
			$qb->andWhere($qb->expr()->eq('p.projectCoordinator', ':coordinatorId'))
				->setParameter('coordinatorId', $coordinatorId);
		}

		return $qb->getQuery()->getArrayResult();
	}

	public function getTotalAgreedAndCostEntity(?string $customerId = null, ?string $managerId = null, ?string $coordinatorId = null): mixed
	{
		if (empty($customerId) && empty($managerId) && empty($coordinatorId)) {
			return [];
		}
		$qb = $this->createQueryBuilder('p');
		$qb
			->select('SUM(p.totalAgreed) as sumTotalAgreed', 'SUM(p.totalCost) as sumTotalCost')
			->where($qb->expr()->eq('p.status', ':statusOpened'))
			->setParameters(new ArrayCollection([
				new Parameter('statusOpened', Project::STATUS_OPEN),
			]));
		if ($customerId) {
			$qb->andWhere($qb->expr()->eq('p.customer', ':customerId'))
				->setParameter('customerId', $customerId);
		}
		if ($managerId) {
			$qb->andWhere($qb->expr()->eq('p.projectManager', ':managerId'))
				->setParameter('managerId', $managerId);
		}
		if ($coordinatorId) {
			$qb->andWhere($qb->expr()->eq('p.projectCoordinator', ':coordinatorId'))
				->setParameter('coordinatorId', $coordinatorId);
		}

		$result = [];
		$queryResult = $qb->getQuery()->getArrayResult();
		if ($queryResult) {
			$result = array_shift($queryResult);
		}

		return $result;
	}

	public function getOpenedProjectsByBranch(?string $branchId = null): mixed
	{
		if (empty($branchId)) {
			return [];
		}

		$qb = $this->createQueryBuilder('p');
		$qb->innerJoin('p.customer', 'c');
		$qb->where($qb->expr()->eq('p.status', ':statusOpened'))
			->setParameters(new ArrayCollection([
				new Parameter('statusOpened', Project::STATUS_OPEN),
			]));
		if ($branchId) {
			$qb->andWhere($qb->expr()->eq('c.branch', ':branchId'))
				->setParameter('branchId', $branchId);
		}

		return $qb->getQuery()->getResult();
	}

	public function getByFilters(array $params): mixed
	{
		$qb = $this->createQueryBuilder('p');
		$qb->select('p');

		if (!empty($params['startDateStart']) && !empty($params['startDateEnd'])) {
			$qb
				->andWhere($qb->expr()->between('p.startDate', ':startDateStart', ':startDateEnd'))
				->setParameter('startDateStart', $params['startDateStart'])
				->setParameter('startDateEnd', $params['startDateEnd']);
		}

		if (!empty($params['customerId'])) {
			$qb
				->andWhere($qb->expr()->eq('p.customer', ':customerId'))
				->setParameter('customerId', $params['customerId']);
		}

		return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}
}
