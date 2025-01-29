<?php

namespace App\Model\Repository;

use App\Model\Entity\AnalyticsProjectStep;
use App\Model\Entity\XtmStatistics;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class XtmStatisticsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(XtmStatistics::class);
		parent::__construct($em, $class);
	}

	public function findByStepAndType(AnalyticsProjectStep $analyticsProjectStep, int $type): ?XtmStatistics
	{
		return $this->findOneBy([
			'step' => $analyticsProjectStep,
			'type' => $type,
		]);
	}
}
