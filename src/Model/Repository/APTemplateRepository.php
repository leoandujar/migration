<?php

namespace App\Model\Repository;

use Doctrine\ORM\Query;
use App\Model\Entity\APTemplate;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;


class APTemplateRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(APTemplate::class);
		parent::__construct($entityManager, $class);
	}

	public function getList(string $id = null, int $targetEntity = null, $start = null, $perPage = null, $sortBy = null, $sortOrder = null): array
	{
		$q = $this->createQueryBuilder('pt');
		$q->select('pt')
			->innerJoin('pt.internalUser', 'iu');
		if ($id) {
			$q->andWhere($q->expr()->eq('iu.id', ':userId'))
				->setParameter('userId', $id);
		}
		if (null !== $targetEntity) {
			$q->andWhere($q->expr()->eq('pt.targetEntity', ':targetEntity'))
				->setParameter('targetEntity', $targetEntity);
		}

		if (null !== $start && null !== $perPage) {
			$q
			 ->setFirstResult($start)
			 ->setMaxResults($perPage);
		}

		if ($sortBy && $sortOrder) {
			$q->orderBy("pt.$sortBy", $sortOrder);
		}

		return $q->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}

	public function getCountSearch(): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('pt');
		$qb->select('COUNT(pt.id)');

		return $qb->getQuery()->getSingleScalarResult();
	}
}
