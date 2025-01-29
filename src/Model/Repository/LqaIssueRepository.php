<?php

namespace App\Model\Repository;

use App\Model\Entity\LqaIssue;
use App\Model\Entity\LqaIssueType;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\LqaIssueTypeMapping;

class LqaIssueRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(LqaIssue::class);
		parent::__construct($em, $class);
	}

	public function findOneByAnalyticsProjectAndType(AnalyticsProject $analyticsProject, LqaIssueType $lqaIssueType): ?LqaIssue
	{
		return $this->findOneBy(['analyticsProject' => $analyticsProject, 'lqaIssueType' => $lqaIssueType]);
	}

	public function findOneByAnalyticsProjectAndTypeMapping(AnalyticsProject $analyticsProject, LqaIssueTypeMapping $lqaIssueTypeMapping): ?LqaIssue
	{
		return $this->findOneBy(['analyticsProject' => $analyticsProject, 'lqaIssueTypeMapping' => $lqaIssueTypeMapping]);
	}
}
