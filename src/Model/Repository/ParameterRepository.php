<?php

namespace App\Model\Repository;

use App\Model\Entity\Parameter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;


class ParameterRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Parameter::class);
		parent::__construct($em, $class);
	}

	public function findByScope(string $scope)
	{
		return $this->findBy(['scope' => $scope]);
	}

	public function findByNameAndScope(string $name, string $scope = null): ?Parameter
	{
		return $this->findOneBy(['name' => $name, 'scope' => $scope]);
	}
}
