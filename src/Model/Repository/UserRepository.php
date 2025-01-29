<?php

namespace App\Model\Repository;

use App\Model\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(User::class);
		parent::__construct($em, $class);
	}

	/**
	 * @return mixed
	 */
	public function findInternal(array $filters): array
	{
		$q = $this->createQueryBuilder('u');
		$q->select(
			'u.firstName',
			'u.lastName',
			'p.name as position',
			'iu.department',
			'g.name as group',
			'u.id as xtrfId',
			'iu.id'
		)
			->innerJoin('u.position', 'p')
			->innerJoin('u.group', 'g')
			->innerJoin('u.internalUser', 'iu')
			->where('u.active = true');

		if (!empty($filters['group'])) {
			$q->andWhere($q->expr()->eq('g.name', ':group'))
				->setParameter('group', $filters['group']);
		}

		if (!empty($filters['department'])) {
			$q->andWhere($q->expr()->eq('iu.department', ':department'))
				->setParameter('department', $filters['department']);
		}

		if (!empty($filters['position'])) {
			$q->andWhere($q->expr()->eq('p.name', ':position'))
				->setParameter('position', $filters['position']);
		}

		return $q->getQuery()->getArrayResult();
	}
}
