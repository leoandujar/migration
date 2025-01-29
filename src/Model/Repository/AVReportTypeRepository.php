<?php

namespace App\Model\Repository;

use App\Model\Entity\AVReportType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;

class AVReportTypeRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(AVReportType::class);
		parent::__construct($entityManager, $class);
	}

	public function getSearch(array $params): mixed
	{
		$qb = $this->createQueryBuilder('rt');
		$qb->select('rt');

		if (isset($params['limit'])) {
			$qb->setMaxResults($params['limit']);
		}

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("rt.{$params['sort_by']}", $params['sort_order']);
		}

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
	}

	public function findByNameOrCode(string $name, string $code): mixed
	{
		$qb = $this->createQueryBuilder('rt');
		$qb->select('rt')
			->where(
				$qb->expr()->orX(
					$qb->expr()->eq('rt.name', ':name'),
					$qb->expr()->eq('rt.code', ':code')
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('name', $name),
				new Parameter('code', $code),
			]));

		return $qb->getQuery()->getOneOrNullResult();
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['code'])) {
			$qb
				->andWhere($qb->expr()->eq('rt.code', ':code'))
				->setParameter('code', $params['code']);
		}
		if (!empty($params['name'])) {
			$qb
				->andWhere($qb->expr()->eq('rt.name', ':name'))
				->setParameter('name', $params['name']);
		}
		if (!empty($params['search'])) {
			$qb
				->andWhere(
					$qb->expr()->orX(
						$qb->expr()->like('LOWER(rt.code)', 'LOWER(:search)'),
						$qb->expr()->like('LOWER(rt.name)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}
	}
}
