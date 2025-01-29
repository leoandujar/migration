<?php

namespace App\Model\Repository;

use App\Model\Entity\Statistics;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\AnalyticsProjectStep;

class StatisticsRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Statistics::class);
		parent::__construct($em, $class);
	}

	public function findByStepAndType(AnalyticsProjectStep $analyticsProjectStep, int $type): ?Statistics
	{
		return $this->findOneBy([
			'step' => $analyticsProjectStep,
			'type' => $type,
		]);
	}
}
