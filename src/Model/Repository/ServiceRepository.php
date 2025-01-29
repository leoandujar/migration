<?php

namespace App\Model\Repository;

use App\Model\Entity\Project;
use App\Model\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class ServiceRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Service::class);
		parent::__construct($em, $class);
	}

	public function getTopServices(string $customerId, \DateTime $date, int $limit = 5): ArrayCollection
	{
		$qb = $this->createQueryBuilder('s');

		$qb->select('s')
		->innerJoin(Project::class, 'p', 'WITH', 'p.service = s.id')
		->where('p.createdOn >= :date')
		->andWhere('p.customer = :customer')
		->groupBy('s.id', 's.name')
		->orderBy('count(s.id)', 'DESC')
		->addOrderBy('s.name', 'ASC')
		->setMaxResults($limit)
		->setParameter('date', $date)
		->setParameter('customer', $customerId);

		$result = $qb->getQuery()->getResult();

		return new ArrayCollection($result);
	}
}
