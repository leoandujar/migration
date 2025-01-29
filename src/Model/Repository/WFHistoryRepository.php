<?php

namespace App\Model\Repository;

use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class WFHistoryRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(WFHistory::class);
		parent::__construct($em, $class);
	}

	public function expires(): mixed
	{
		$q = $this->createQueryBuilder('h');
		$q->where('h.expiresAt <= CURRENT_DATE()')
			->andWhere('h.removed = false')
		;

		return $q->getQuery()->getResult();
	}

	public function getHistories(int $days): mixed
	{
		$now = new \DateTime();
		$old = $now->sub(new \DateInterval(sprintf('P%dD', $days)));
		$q   = $this->createQueryBuilder('h');
		$q->where('h.createdAt <= :old')
			->andWhere('h.removed = false')
			->setParameter('old', $old->format('Y-m-d H:i:s'))
		;

		return $q->getQuery()->getResult();
	}
}
