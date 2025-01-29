<?php

namespace App\Model\Repository;

use App\Model\Entity\WFParams;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkflowParamsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(WFParams::class);
		parent::__construct($em, $class);
	}

	public function getHigherId(): mixed
	{
		$qb = $this->createQueryBuilder('wfp');
		$qb->select('MAX(wfp.id)')->setMaxResults(1);

		$result = $qb->getQuery()->getScalarResult();
		if ($result) {
			$shift = array_shift($result);

			return $shift[1];
		}

		return null;
	}
}
