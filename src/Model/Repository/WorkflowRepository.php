<?php

namespace App\Model\Repository;

use App\Model\Entity\WFWorkflow;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkflowRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(WFWorkflow::class);
		parent::__construct($em, $class);
	}

	public function getSearch(array $params): mixed
	{
		$qb = $this->createQueryBuilder('wf');
		$qb->select(
			'wf',
			'cg',
			'wfp'
		)
		->leftJoin('wf.parameters', 'wfp')
		->leftJoin('wf.categoryGroups', 'cg');

		$qb
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("wf.{$params['sort_by']}", $params['sort_order']);
		}

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult();
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['workflow_type'])) {
			$qb
				->andWhere($qb->expr()->in('wf.type', ':workflow_type'))
				->setParameter('workflow_type', $params['workflow_type']);
		}
		if (!empty($params['category_groups'])) {
			$qb
				->andWhere($qb->expr()->in('cg.code', ':category_groups'))
				->setParameter('category_groups', $params['category_groups']);
		}
		if (null !== $params['run_automatically']) {
			$qb
				->andWhere($qb->expr()->eq('wf.runAutomatically', ':run_automatically'))
				->setParameter('run_automatically', $params['run_automatically']);
		}
		if (!empty($params['run_pattern'])) {
			$qb
				->andWhere($qb->expr()->eq('wf.runFrequency', ':runFrequency'))
				->setParameter('runFrequency', $params['run_pattern']);
		}
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->eq('wf.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (!empty($params['notification_type'])) {
			$qb
				->andWhere(
					$qb->expr()->in('wfp.notificationType', ':notification_type')
				)
				->setParameter('notification_type', $params['notification_type']);
		}
		if (!empty($params['search'])) {
			$qb
				->andWhere(
					$qb->expr()->orX(
						$qb->expr()->like('LOWER(wf.type)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(wf.name)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}
	}

	public function getCountRows(): int
	{
		$q = $this->createQueryBuilder('wf');
		$q->select('COUNT(wf.id)');

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}

	public function getHigherId(): mixed
	{
		$qb = $this->createQueryBuilder('wf');
		$qb->select('MAX(wf.id)')->setMaxResults(1);

		$result = $qb->getQuery()->getScalarResult();
		if ($result) {
			$shift = array_shift($result);

			return $shift[1];
		}

		return null;
	}
}
