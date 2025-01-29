<?php

namespace App\Model\Repository;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\Workflow;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class WorkflowMonitorRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(AVWorkflowMonitor::class);
		parent::__construct($em, $class);
	}

	public function getList(array $params): ?array
	{
		$q = $this->createQueryBuilder('wm');
		$q->select('wm')
			->leftJoin('wm.workflow', 'wf')
			->leftJoin('wf.categoryGroups', 'cg')
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$target = "wm.{$params['sort_by']}";
			if (!property_exists(AVWorkflowMonitor::class, $params['sort_by'])) {
				if (property_exists(Workflow::class, $params['sort_by'])) {
					$target = "wf.{$params['sort_by']}";
				}
			}
			$q->orderBy("$target", $params['sort_order']);
		}

		if (!empty($params['search'])) {
			$q
				->andWhere(
					$q->expr()->orX(
						$q->expr()->like('LOWER(wm.status)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}

		if (count($params['status'])) {
			$q
				->andWhere($q->expr()->in('wm.status', ':status'))
				->setParameter('status', $params['status']);
		}

		if (!empty($params['workflow_id'])) {
			$q
				->andWhere($q->expr()->in('wf.id', ':workflow_id'))
				->setParameter('workflow_id', $params['workflow_id']);
		}

		if (!empty($params['category_groups'])) {
			$q
				->andWhere($q->expr()->in('cg.code', ':category_groups'))
				->setParameter('category_groups', $params['category_groups']);
		}

		if ($params['internal_user_id']) {
			$q
				->andWhere($q->expr()->eq('wm.createdBy', ':internalUserId'))
				->setParameter('internalUserId', $params['internal_user_id']);
		}

		return $q->getQuery()->getResult();
	}

	public function getCountRows(): int
	{
		$q = $this->createQueryBuilder('wm');
		$q->select('COUNT(wm.id)');

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}
}
