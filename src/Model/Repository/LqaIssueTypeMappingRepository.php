<?php

namespace App\Model\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\LqaIssueTypeMapping;
use Doctrine\ORM\NonUniqueResultException;

class LqaIssueTypeMappingRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(LqaIssueTypeMapping::class);
		parent::__construct($em, $class);
	}

	public function findOneByNameAndParent(string $name, LqaIssueTypeMapping $parent = null): ?LqaIssueTypeMapping
	{
		$q = $this->createQueryBuilder('laqITM');
		$q->select('laqITM')
			->where('laqITM.name = :name')
			->setParameter('name', $name);

		if (null === $parent) {
			$q->andWhere($q->expr()->isNull('laqITM.parent'));
		} else {
			$q->andWhere($q->expr()->eq('laqITM.parent', ':parent'))
				->setParameter('parent', $parent);
		}

		try {
			return $q->getQuery()->getOneOrNullResult();
		} catch (NonUniqueResultException $e) {
			return null;
		}
	}
}
