<?php

namespace App\Model\Repository;

use App\Model\Entity\CategoryGroup;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryGroupRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(CategoryGroup::class);
		parent::__construct($em, $class);
	}

	public function getSearch(array $params): mixed
	{
		$qb = $this->createQueryBuilder('cg');
		$qb
			->select('cg')
		->orderBy('cg.name');

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult();
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['target'])) {
			$qb
				->andWhere($qb->expr()->eq('cg.target', ':target'))
				->setParameter('target', $params['target']);
		}
		if (!empty($params['code'])) {
			$qb
				->andWhere($qb->expr()->eq('cg.code', ':code'))
				->setParameter('code', $params['code']);
		}
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->eq('cg.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (isset($params['active']) && null !== $params['active']) {
			$qb
				->andWhere($qb->expr()->eq('cg.active', ':active'))
				->setParameter('active', $params['active']);
		}
	}
}
