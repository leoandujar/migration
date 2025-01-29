<?php

namespace App\Model\Repository;

use App\Model\Entity\Activity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class ActivityRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Activity::class);
		parent::__construct($em, $class);
	}

	public function getActivities(string $partialName, int $limit, array $types = null): ?array
	{
		$q = $this->createQueryBuilder('a');
		$q->select(
			'a.id,
			a.projectPhaseIdNumber as activity,
			p.name as provider,
			t.name as type'
		)
			->innerJoin('a.provider', 'p')
			->innerJoin('a.activityType', 't')
			->where(
				$q->expr()->like('LOWER(a.projectPhaseIdNumber)', 'LOWER(:partialName)')
			)
			->setParameters(new ArrayCollection([
				new Parameter('partialName', "%$partialName%"),
			]))
			->setMaxResults($limit);

		if ($types) {
			$q->andWhere($q->expr()->in('a.activityType', ':typeIds'))
				->setParameter('typeIds', $types);
		}

		return $q->getQuery()->getArrayResult();
	}
}
