<?php

namespace App\Model\Repository;

use App\Model\Entity\InternalUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class InternalUserRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(InternalUser::class);
		parent::__construct($em, $class);
	}

	/**
	 * @return mixed
	 */
	public function findByTag(string $tag): array
	{
		$q = $this->createQueryBuilder('iu');
		$q->select('iu.mobile')
			->where($q->expr()->like('CAST(iu.tag AS text)', ':tag'))
			->setParameter('tag', "%$tag%");

		return $q->getQuery()->getArrayResult();
	}

	/**
	 * @return mixed
	 */
	public function findByRoles(array $params): array
	{
		$q = $this->createQueryBuilder('iu');

		if (!empty($params['roles'])) {
			$orX = $q->expr()->orX();
			foreach ($params['roles'] as $key => $role) {
				$orX->add("CONTAINS(iu.roles, :role$key) = true");
				$q->setParameter("role$key", '["'.strtoupper($role).'"]');
			}
			$q->andWhere($orX);
		}

		return $q->getQuery()->getResult();
	}

	/**
	 * @return mixed
	 */
	public function findInternal(): array
	{
		$q = $this->createQueryBuilder('iu');
		$q->select(
			'iu.firstName',
			'iu.lastName',
			'iu.id',
		)
			->where($q->expr()->eq('iu.type', 1))
			->andWhere($q->expr()->eq('iu.status', 1));

		return $q->getQuery()->getArrayResult();
	}

	public function findForPublicLogin(string $startDate, string $endDate): array
	{
		$q = $this->createQueryBuilder('iu');
		$q->select('iu')
			->where(
				$q->expr()->andX(
					$q->expr()->eq('iu.type', ':typePublic'),
					$q->expr()->between('iu.lastLoginDate', ':startDate', ':endDate')
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('startDate', $startDate),
				new Parameter('endDate', $endDate),
				new Parameter('typePublic', InternalUser::TYPE_PUBLIC),
			]));

		return $q->getQuery()->getResult();
	}
}
