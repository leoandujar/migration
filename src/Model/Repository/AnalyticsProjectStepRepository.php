<?php

namespace App\Model\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\AnalyticsProjectStep;

class AnalyticsProjectStepRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(AnalyticsProjectStep::class);
		parent::__construct($entityManager, $class);
	}

	public function findByProjectLangAndName(AnalyticsProject $analyticsProject, string $languageCode, $name): ?AnalyticsProjectStep
	{
		return $this->findOneBy([
			'analyticsProject'   => $analyticsProject,
			'targetLanguageCode' => $languageCode,
			'name'               => $name,
		]);
	}
}
