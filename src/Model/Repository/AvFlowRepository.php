<?php

namespace App\Model\Repository;

use App\Model\Entity\AvFlow;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AvFlowRepository extends EntityRepository
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(AvFlow::class);
		parent::__construct($em, $class);
		$this->em = $em;
	}

	public function getWithFilters(array $params): mixed
	{
		$qb = $this->createQueryBuilder('av_flow');
		$qb->select('av_flow', 'cg')
			->leftJoin('av_flow.categoryGroups', 'cg');

		$qb->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("av_flow.{$params['sort_by']}", $params['sort_order']);
		}

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult();
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['category_groups'])) {
			$qb->andWhere($qb->expr()->in('cg.code', ':category_groups'))
				->setParameter('category_groups', $params['category_groups']);
		}
		if (null !== $params['run_automatically']) {
			$qb->andWhere($qb->expr()->eq('av_flow.runAutomatically', ':run_automatically'))
				->setParameter('run_automatically', $params['run_automatically']);
		}
		if (!empty($params['run_pattern'])) {
			$qb->andWhere($qb->expr()->eq('av_flow.runFrequency', ':runFrequency'))
				->setParameter('runFrequency', $params['run_pattern']);
		}
		if (!empty($params['name'])) {
			$qb->andWhere($qb->expr()->eq('av_flow.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (!empty($params['search'])) {
			$qb->andWhere(
				$qb->expr()->orX(
					$qb->expr()->like('LOWER(av_flow.description)', 'LOWER(:search)'),
					$qb->expr()->like('LOWER(av_flow.name)', 'LOWER(:search)')
				)
			)
				->setParameter('search', "%{$params['search']}%");
		}
	}

	public function getCountRows(array $params): int
	{
		$q = $this->createQueryBuilder('av_flow');
		$q->select('COUNT(av_flow.id)');

		$this->searchFilter($params, $q);

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}
}
