<?php

namespace App\Model\Repository;

use App\Model\Entity\TaskReview;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Parameter;

class TaskReviewRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(TaskReview::class);
		parent::__construct($em, $class);
	}

	public function countTaskForReview(string $projectId): bool|float|int|string|null
	{
		$q = $this->createQueryBuilder('tr');
		$q->select('COUNT(tr)')
			->innerJoin('tr.task', 't')
			->innerJoin('t.project', 'p')
			->where($q->expr()->eq('p.id', ':projectId'))
			->setParameters(new ArrayCollection([
				new Parameter('projectId', $projectId),
			]));
		try {
			return $q->getQuery()->getSingleScalarResult();
		} catch (NonUniqueResultException $e) {
			return null;
		}
	}
}
