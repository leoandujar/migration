<?php

namespace App\Model\Repository;

use App\Model\Entity\AnalyticsProject;
use App\Model\Entity\XtmMetrics;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;

class XtmMetricsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(XtmMetrics::class);
		parent::__construct($em, $class);
	}

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
