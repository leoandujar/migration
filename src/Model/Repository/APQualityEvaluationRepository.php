<?php

namespace App\Model\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Entity\APQualityEvaluation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class APQualityEvaluationRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(APQualityEvaluation::class);
		parent::__construct($entityManager, $class);
	}

	public function list(array $filter): ?array
	{
		$q = $this->createQueryBuilder('e');
		$q->select('e')
			->setFirstResult($filter['start'])
			->setMaxResults($filter['limit']);

		if (isset($filter['sort_by']) && isset($filter['sort_order'])) {
			$q->orderBy("e.{$filter['sort_by']}", $filter['sort_order']);
		}

		$this->getFilter($q, $filter);

		return $q->getQuery()->getResult();
	}

	public function totalRows(array $filter): int
	{
		$q = $this->createQueryBuilder('e');
		$q->select('COUNT(e.id)');

		if (isset($filter['sort_by']) && isset($filter['sort_order'])) {
			$q->orderBy("e.{$filter['sort_by']}", $filter['sort_order']);
		}

		$this->getFilter($q, $filter);

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}

	private function getFilter(QueryBuilder $q, $filter): void
	{
		if (!empty($filter['search'])) {
			$q
				->leftJoin('e.evaluatee', 'evaluatee')
				->leftJoin('e.evaluator', 'evaluator')
				->andWhere(
					$q->expr()->orX(
						$q->expr()->like('LOWER(evaluatee.firstName)', 'LOWER(:search)'),
						$q->expr()->like('LOWER(evaluator.firstName)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$filter['search']}%");
		}

		if (count($filter['status'])) {
			$q
				->andWhere($q->expr()->in('e.status', ':status'))
				->setParameter('status', $filter['status']);
		}

		if (!empty($filter['type'])) {
			$q
				->andWhere($q->expr()->eq('e.type', ':type'))
				->setParameter('type', $filter['type']);
		}
	}
}
