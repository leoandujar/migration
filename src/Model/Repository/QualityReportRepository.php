<?php

namespace App\Model\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\QualityReport;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class QualityReportRepository extends EntityRepository
{
	
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(QualityReport::class);
		parent::__construct($em, $class);
	}

	public function findReport(int $dqaReportId): ?array
	{
		$q = $this->createQueryBuilder('dqaR');
		$q->select('dqaR, dqaIssues, dqaC')
			->leftJoin('dqaR.qualityIssues', 'dqaIssues')
			->innerJoin('dqaIssues.qualityCategory', 'dqaC')
			->where(
				$q->expr()->eq('dqaR.id', ':dqaReportId')
			)
			->setParameters(new ArrayCollection([
				new Parameter('dqaReportId', $dqaReportId)
			]));

		return $q->getQuery()->getArrayResult();
	}

	public function findReportByActivity(int $activityId): ?array
	{
		$q = $this->createQueryBuilder('dqaR');
		$q->select('dqaR, dqaIssues, dqaC')
			->leftJoin('dqaR.qualityIssues', 'dqaIssues')
			->innerJoin('dqaIssues.qualityCategory', 'dqaC')
			->where(
				$q->expr()->eq('dqaR.activity', ':activityId')
			)
			->setParameters(new ArrayCollection([
				new Parameter('activityId', $activityId)
			]));

		return $q->getQuery()->getArrayResult();
	}

	public function findReports(array $params): ?array
	{
		$q = $this->createQueryBuilder('dqaR');
		$q->select(
			'dqaR.id',
			'dqaR.pageCount',
			'dqaR.status',
			'a.projectPhaseIdNumber as activity',
			'a.id as activityId',
			'p.name as provider',
			'COUNT(dqaIssues) as issuesCount'
		)
			->leftJoin('dqaR.qualityIssues', 'dqaIssues')
			->innerJoin('dqaR.activity', 'a')
			->innerJoin('a.provider', 'p')
			->groupBy('dqaR.id', 'a.projectPhaseIdNumber', 'a.id', 'p.name')
		->setFirstResult($params['start'])
		->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$q->orderBy("dqaR.{$params['sort_by']}", $params['sort_order']);
		}

		if (!empty($params['search'])) {
			$q
				->andWhere(
					$q->expr()->orX(
						$q->expr()->like('LOWER(p.name)', 'LOWER(:search)'),
						$q->expr()->like('LOWER(a.projectPhaseIdNumber)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}

		if (count($params['status'])) {
			$q
				->andWhere($q->expr()->in('dqaR.status', ':status'))
				->setParameter('status', $params['status']);
		}

		if (!empty($params['type'])) {
			$q
				->andWhere($q->expr()->eq('dqaR.type', ':type'))
				->setParameter('type', $params['type']);
		}

		return $q->getQuery()->getArrayResult();
	}

	public function getCountReports(array $params): int
	{
		$q = $this->createQueryBuilder('dqaR');
		$q->select('COUNT(DISTINCT dqaR.id)')
			->leftJoin('dqaR.qualityIssues', 'dqaIssues')
			->innerJoin('dqaR.activity', 'a')
			->innerJoin('a.provider', 'p');

		if (!empty($params['search'])) {
			$q
				->andWhere(
					$q->expr()->orX(
						$q->expr()->like('LOWER(p.name)', 'LOWER(:search)'),
						$q->expr()->like('LOWER(a.projectPhaseIdNumber)', 'LOWER(:search)')
					)
				)
				->setParameter('search', "%{$params['search']}%");
		}

		if (count($params['status'])) {
			$q
				->andWhere($q->expr()->in('dqaR.status', ':status'))
				->setParameter('status', $params['status']);
		}

		if (!empty($params['type'])) {
			$q
				->andWhere($q->expr()->eq('dqaR.type', ':type'))
				->setParameter('type', $params['type']);
		}

		return $q->getQuery()->getSingleScalarResult() ?? 0;
	}
}
