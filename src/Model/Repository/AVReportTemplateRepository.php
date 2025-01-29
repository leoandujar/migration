<?php

namespace App\Model\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\AVReportTemplate;
use Doctrine\ORM\EntityManagerInterface;

class AVReportTemplateRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(AVReportTemplate::class);
		parent::__construct($entityManager, $class);
	}

	public function getSearch(array $params): mixed
	{
		$qb = $this->createQueryBuilder('rt');
		$qb
			->select('rt')
			->leftJoin('rt.chartList', 'chl')
			->leftJoin('chl.chart', 'c')
			->groupBy('rt.id');

		if (isset($params['limit'])) {
			$qb->setMaxResults($params['limit']);
		}

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("rt.{$params['sort_by']}", $params['sort_order']);
		}

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->eq('rt.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (!empty($params['format'])) {
			$qb
				->andWhere(
					$qb->expr()->in('rt.format', ':format')
				)
				->setParameter('format', $params['format']);
		}
		if (!empty($params['groups'])) {
			$orX = $qb->expr()->orX();
			foreach ($params['groups'] as $key => $groupCode) {
				$orX->add("CONTAINS(rt.categoryGroups, :group$key) = true");
				$qb->setParameter("group$key", '["'.strtoupper($groupCode).'"]');
			}
			$qb->andWhere($orX);
		}
		if (!empty($params['search'])) {
			$qb
				->andWhere(
					$qb->expr()->orX(
						$qb->expr()->like('LOWER(rt.name)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(rt.format)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(c.code)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(c.type)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}
	}
}
