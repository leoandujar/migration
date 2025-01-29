<?php

namespace App\Model\Repository;

use Doctrine\ORM\Query;
use App\Model\Entity\Permission;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class PermissionsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Permission::class);
		parent::__construct($em, $class);
	}

	
	public function getActionsListByUserOrRoles($target, string $userType = Permission::TARGET_ADMIN_PORTAL)
	{
		$q = $this->createQueryBuilder('p');
		$q->select(
			'a.code',
			'a.id',
			'a.name',
			'p.active'
		)->innerJoin('p.action', 'a');

		if (!is_array($target)) {
			if (Permission::TARGET_ADMIN_PORTAL === $userType) {
				$q->leftJoin('p.internalUser', 'u')
					->where($q->expr()->eq('u.id', ':target'));
			} else {
				$q->leftJoin('p.cpUser', 'u')
					->where($q->expr()->eq('u.id', ':target'));
			}
		} else {
			$q->leftJoin('p.role', 'role')
				->where($q->expr()->in('role.code', ':target'));
		}
		$q->orderBy('a.code', 'ASC');
		$q->setParameter('target', $target);

		return $q->getQuery()->getArrayResult();
	}


	public function getActionsByUserOrRoles($target, string $userType = Permission::TARGET_ADMIN_PORTAL)
	{
		$result = [];
		$q = $this->createQueryBuilder('p');
		$q->select(
			'a.code',
			'p.active'
		)->innerJoin('p.action', 'a');

		if (!is_array($target)) {
			if (Permission::TARGET_ADMIN_PORTAL === $userType) {
				$q->leftJoin('p.internalUser', 'u')
					->where($q->expr()->eq('u.id', ':target'));
			} else {
				$q->leftJoin('p.cpUser', 'u')
					->where($q->expr()->eq('u.id', ':target'));
			}
		} else {
			$q->leftJoin('p.role', 'role')
				->where($q->expr()->in('role.code', ':target'));
		}

		$q->setParameter('target', $target);
		$data = $q->getQuery()->getResult(Query::HYDRATE_ARRAY);
		foreach ($data as $datum) {
			$result[$datum['code']] = $datum['active'];
		}

		return $result;
	}

	public function getActionsByCustomerOrRoles($target): array
	{
		$result = [];
		$q = $this->createQueryBuilder('p');
		$q->select(
			'a.code',
			'p.active'
		)->innerJoin('p.action', 'a');

		if (!is_array($target)) {
			$q->leftJoin('p.cpCustomer', 'c')
				->where($q->expr()->eq('c.id', ':target'));
		} else {
			$q->leftJoin('p.role', 'role')
				->where($q->expr()->in('role.code', ':target'));
		}
		$q->orderBy('a.code', 'ASC');
		$q->setParameter('target', $target);
		$data = $q->getQuery()->getResult(Query::HYDRATE_ARRAY);
		foreach ($data as $datum) {
			$result[$datum['code']] = $datum['active'];
		}

		return $result;
	}
}
