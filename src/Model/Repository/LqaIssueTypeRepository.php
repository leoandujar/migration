<?php

namespace App\Model\Repository;

use App\Model\Entity\LqaIssueType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;


class LqaIssueTypeRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(LqaIssueType::class);
		parent::__construct($em, $class);
	}

	public function findOneByNameAndParent(string $name, LqaIssueType $parent = null): ?LqaIssueType
	{
		$q = $this->createQueryBuilder('laqIT');
		$q->select('laqIT')
			->where('laqIT.name = :name')
			->setParameter('name', $name);

		if (null === $parent) {
			$q->andWhere($q->expr()->isNull('laqIT.parent'));
		} else {
			$q->andWhere($q->expr()->eq('laqIT.parent', ':parent'))
				->setParameter('parent', $parent);
		}

		try {
			return $q->getQuery()->getOneOrNullResult();
		} catch (NonUniqueResultException $e) {
			return null;
		}
	}

	public function findOneByName(string $name): ?LqaIssueType
	{
		return $this->findOneBy([
			'name' => $name,
		]);
	}
}
