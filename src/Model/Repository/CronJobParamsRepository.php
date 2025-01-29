<?php

namespace App\Model\Repository;

use App\Model\Entity\WFParams;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;


class CronJobParamsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(WFParams::class);
		parent::__construct($em, $class);
	}

	public function findByCron(string $command): mixed
	{
		$q = $this->createQueryBuilder('p');
		$q
			->innerJoin('p.workflow', 'workflow')
			->where('workflow.name = :command')
			->setParameter('command', $command)
			->setMaxResults(1);

		return $q->getQuery()->getOneOrNullResult();
	}
}
