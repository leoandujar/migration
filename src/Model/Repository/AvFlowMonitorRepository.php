<?php

namespace App\Model\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Entity\AvFlowMonitor;
use App\Model\Entity\AvFlow;
use Doctrine\ORM\EntityManagerInterface;

class AvFlowMonitorRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(AvFlowMonitor::class);
		parent::__construct($em, $class);
	}

	public function getList(array $params): ?array
	{
		$qb = $this->createQueryBuilder('fm');
		$qb->select('fm')
			->leftJoin('fm.flow', 'flow')
			->leftJoin('flow.categoryGroups', 'cg')
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$target = "fm.{$params['sort_by']}";
			if (!property_exists(AvFlowMonitor::class, $params['sort_by'])) {
				if (property_exists(AvFlow::class, $params['sort_by'])) {
					$target = "wf.{$params['sort_by']}";
				}
			}
			$qb->orderBy("$target", $params['sort_order']);
		}

		$this->applyFilters($params, $qb);

		return $qb->getQuery()->getResult();
	}

	public function applyFilters(array $params, &$qb)
	{
		if (!empty($params['search'])) {
			$qb
				->andWhere(
					$qb->expr()->orX(
						$qb->expr()->like('LOWER(fm.status)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}

		if (count($params['status'] ?? [])) {
			$qb
				->andWhere($qb->expr()->in('fm.status', ':status'))
				->setParameter('status', $params['status']);
		}

		if (!empty($params['flow_id'])) {
			$qb
				->andWhere($qb->expr()->in('fm.flow', ':flow_id'))
				->setParameter('flow_id', $params['flow_id']);
		}

		if (!empty($params['category_groups'])) {
			$qb
				->andWhere($qb->expr()->in('cg.code', ':category_groups'))
				->setParameter('category_groups', $params['category_groups']);
		}

		if ($params['internal_user_id'] ?? null) {
			$qb
				->andWhere($qb->expr()->eq('fm.requestedBy', ':internalUserId'))
				->setParameter('internalUserId', $params['internal_user_id']);
		}
	}

	public function getCountRows(array $params): int
	{
		$qb = $this->createQueryBuilder('fm');
		$qb->select('COUNT(fm.id)');

		$this->applyFilters($params, $qb);

		return $qb->getQuery()->getSingleScalarResult() ?? 0;
	}
}
