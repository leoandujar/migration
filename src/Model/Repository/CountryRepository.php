<?php

namespace App\Model\Repository;

use App\Model\Entity\Country;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CountryRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Country::class);
		parent::__construct($em, $class);
	}

	public function getList()
	{
		$q = $this->createQueryBuilder('c');
		$q
			->select('c.id,c.name')
			->orderBy('c.name', 'ASC');

		return $q->getQuery()->getArrayResult();
	}
}
