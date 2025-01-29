<?php

namespace App\Model\Repository;

use App\Model\Entity\AVParameter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class AVParameterRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(AVParameter::class);
		parent::__construct($em, $class);
	}

	public function getList(string $name = null, string $scope = null, $start = null, $perPage = null, $sortBy = null, $sortOrder = null): array
	{
		$q = $this->createQueryBuilder('avp');
		$q->select('avp');
		if (!empty($name)) {
			$q
				->andWhere($q->expr()->eq('avp.name', ':name'))
				->setParameter('name', $name);
		}
		if (!empty($scope)) {
			$q
				->andWhere($q->expr()->eq('avp.scope', ':scope'))
				->setParameter('scope', $scope);
		}
		if (null !== $start && null !== $perPage) {
			$q
			 ->setFirstResult($start)
			 ->setMaxResults($perPage);
		}

		if (null !== $sortBy && null !== $sortOrder) {
			$q->orderBy("avp.$sortBy", $sortOrder);
		}

		return $q->getQuery()->getResult();
	}

	public function getCountSearch(): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('avp');
		$qb->select('COUNT(avp.id)');

		return $qb->getQuery()->getSingleScalarResult();
	}
}
