<?php

namespace App\Model\Repository;

use App\Model\Entity\CPTemplate;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CPTemplateRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(CPTemplate::class);
		parent::__construct($entityManager, $class);
	}

	public function getCountOfTemplates(array $ids = [], int $type = null): int
	{
		$qb = $this->createQueryBuilder('pt');
		$qb->select('COUNT(pt.id)')
			->innerJoin('pt.contactPerson', 'cp');
		if ($ids) {
			$qb->andWhere($qb->expr()->in('cp.id', ':ids'))
				->setParameter('ids', $ids);

			$this->templatesFilter($ids, $type, $qb, null);
		}

		return $qb->getQuery()->getSingleScalarResult() ?? 0;
	}

	public function getCountByCustomerAndContactPerson(array $ids, int $type1, int $userId, int $type2, ?string $search): bool|int|string|null
	{
		$qb = $this->createQueryBuilder('pt');

		$qb->select('COUNT(pt.id)')
			->innerJoin('pt.contactPerson', 'cp');

		$this->templatesFilterByCustomerAndContactPerson($ids, $type1, $userId, $type2, $qb, $search);

		return $qb->getQuery()->getSingleScalarResult();
	}

	public function getByCustomerAndContactPerson(array $ids, int $type1, int $userId, int $type2, int $start = null, int $perPage = null, string $sortBy = null, string $sortOrder = null, string $search = null): array
	{
		$qb = $this->createQueryBuilder('pt');

		$qb->select('pt')
			->innerJoin('pt.contactPerson', 'cp');

		$this->templatesFilterByCustomerAndContactPerson($ids, $type1, $userId, $type2, $qb, $search);

		if (null !== $start && null !== $perPage) {
			$qb
				->setFirstResult($start)
				->setMaxResults($perPage);
		}
		if ($sortBy && $sortOrder) {
			$qb->orderBy("pt.$sortBy", $sortOrder);
		}

		return $qb->getQuery()->getResult();
	}

	public function getByContactPerson(array $ids = [], int $type = null, $start = null, $perPage = null, $sortBy = null, $sortOrder = null, string $search = null): array
	{
		$qb = $this->createQueryBuilder('pt');
		$qb->select('pt')
			->innerJoin('pt.contactPerson', 'cp');

		$this->templatesFilter($ids, $type, $qb, $search);

		if (null !== $start && null !== $perPage) {
			$qb
				->setFirstResult($start)
				->setMaxResults($perPage);
		}

		if ($sortBy && $sortOrder) {
			$qb->orderBy("pt.$sortBy", $sortOrder);
		}

		return $qb->getQuery()->getResult();
	}

	public function templatesFilter(array $ids, ?int $type1, $qb, ?string $search): void
	{
		if ($ids) {
			$qb->andWhere($qb->expr()->in('cp.id', ':ids'))
				->setParameter('ids', $ids);
		}
		if (null !== $type1) {
			$qb->andWhere($qb->expr()->eq('pt.type', ':type'))
				->setParameter('type', $type1);
		}
		if (null !== $search) {
			$qb->andWhere($qb->expr()->like('LOWER(pt.name)', 'LOWER(:search)'))
				->setParameter('search', "%$search%");
		}
	}

	public function templatesFilterByCustomerAndContactPerson(array $ids, int $type1, int $userId, int $type2, $qb, ?string $search): void
	{
		$qb->where(
			$qb->expr()->orX(
				$qb->expr()->andX(
					$qb->expr()->in('cp.id', ':ids'),
					$qb->expr()->eq('pt.type', ':type1')
				),
				$qb->expr()->andX(
					$qb->expr()->eq('cp.id', ':userId'),
					$qb->expr()->eq('pt.type', ':type2')
				)
			)
		)
			->setParameter('ids', $ids)
			->setParameter('userId', $userId)
			->setParameter('type1', $type1)
			->setParameter('type2', $type2);

		if (null !== $search) {
			$qb->andWhere($qb->expr()->like('LOWER(pt.name)', 'LOWER(:search)'))
				->setParameter('search', "%$search%");
		}
	}

	public function getCountSearch(): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('pt');
		$qb->select('COUNT(pt.id)');

		return $qb->getQuery()->getSingleScalarResult();
	}
}
