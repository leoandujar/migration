<?php

namespace App\Model\Repository;

use App\Model\Entity\Metrics;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;

class MetricsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Metrics::class);
		parent::__construct($em, $class);
	}

	/**
	 * @return Metrics[] Returns an array of Metrics objects
	 *
	 * @throws NonUniqueResultException
	 */
	public function findByAnalyticsProjectAndLang($id, $targetLanguageCode)
	{
		return $this->createQueryBuilder('m')
			->leftJoin(AnalyticsProject::class, 'a', Join::WITH, 'm.analyticsProject = a.id')
			->andWhere('a.id = :id')
			->andWhere('m.targetLanguageCode = :targetLanguageCode')
			->setParameter('id', $id)
			->setParameter('targetLanguageCode', $targetLanguageCode)
			->orderBy('m.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getOneOrNullResult();
	}

	public function getWordCountByAnalyticsProject(AnalyticsProject $analyticsProject): bool|float|int|string|null
	{
		return $this->createQueryBuilder('m')
			->select('m.totalWords')
			->andWhere('m.analyticsProject = :analyticsProject')
			->setParameter('analyticsProject', $analyticsProject)
			->getQuery()
			->getSingleScalarResult();
	}
}
