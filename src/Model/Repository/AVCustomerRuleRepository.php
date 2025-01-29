<?php

namespace App\Model\Repository;

use App\Model\Entity\AVCustomerRule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AVCustomerRuleRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(AVCustomerRule::class);
		parent::__construct($em, $class);
	}

	public function getList($params): array
	{
		$qb = $this->createQueryBuilder('cr');
		$qb->select('cr')
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("cr.{$params['sort_by']}", $params['sort_order']);
		}

		$this->customerRuleFilter($params, $qb);

		return $qb->getQuery()->getResult();
	}

	public function customerRuleFilter(array $params, &$qb): void
	{
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->like('LOWER(cr.name)', 'LOWER(:name)'))
				->setParameter('name', $params['name']);
		}
		if (!empty($params['event'])) {
			$qb
				->andWhere($qb->expr()->like('LOWER(cr.event)', 'LOWER(:event)'))
				->setParameter('event', $params['event']);
		}
		if (!empty($params['type'])) {
			$qb
				->andWhere($qb->expr()->like('LOWER(cr.type)', 'LOWER(:type)'))
				->setParameter('type', $params['type']);
		}
	}

	public function getCountRows(): int
	{
		$q = $this->createQueryBuilder('cr');
		$q->select('COUNT(cr.id)');

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}
}
