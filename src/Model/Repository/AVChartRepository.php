<?php

namespace App\Model\Repository;

use App\Model\Entity\AVChart;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;


class AVChartRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(AVChart::class);
		parent::__construct($entityManager, $class);
	}

	public function getSearch(array $params): mixed
	{
		$qb = $this->createQueryBuilder('ch');
		$qb
		->select('ch')
		->setFirstResult($params['start'])
		->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("ch.{$params['sort_by']}", $params['sort_order']);
		}

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult();
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['code'])) {
			$qb
				->andWhere($qb->expr()->eq('ch.code', ':code'))
				->setParameter('code', $params['code']);
		}
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->eq('ch.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (isset($params['active'])) {
			$qb
				->andWhere($qb->expr()->eq('ch.active', ':active'))
				->setParameter('active', $params['active']);
		}
		if (!empty($params['report_type_id'])) {
			$qb
				->andWhere(
					$qb->expr()->in('ch.reportType', ':report_type_id')
				)
				->setParameter('report_type_id', $params['report_type_id']);
		}
		if (!empty($params['search'])) {
			$qb
				->andWhere(
					$qb->expr()->orX(
						$qb->expr()->like('LOWER(ch.code)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(ch.name)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(ch.category)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(ch.category)', 'LOWER(:search)'),
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}
	}

	public function getCountRows(): int
	{
		$q = $this->createQueryBuilder('ch');
		$q->select('COUNT(ch.id)');

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}
}
