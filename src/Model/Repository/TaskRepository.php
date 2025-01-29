<?php

namespace App\Model\Repository;

use App\Model\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\CalculationUnit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class TaskRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Task::class);
		parent::__construct($em, $class);
	}

	public function getEntries($date, $source, $entity, $closeDate)
	{
		$qb = $this->createQueryBuilder('t');
		$qb->select('t.externalId as id')
			->addSelect(sprintf("'%s' as date", $date))
			->addSelect(sprintf("'%s' as source", $source))
			->addSelect(sprintf("'%s' as entity", $entity))
			->where($qb->expr()->orX(
				$qb->expr()->isNull('t.closeDate'),
				't.closeDate > :closeDate'
			))->setParameter('closeDate', $closeDate);

		return $qb->getQuery()->getArrayResult();
	}

	public function getEntriesFoDailyUpdate($date, $source, $entity, $startDate)
	{
		$qb = $this->createQueryBuilder('t');
		$qb->select('t.externalId as id')
			->addSelect(sprintf("'%s' as date", $date))
			->addSelect(sprintf("'%s' as source", $source))
			->addSelect(sprintf("'%s' as entity", $entity))
			->where($qb->expr()->orX(
				$qb->expr()->isNull('t.startDate'),
				't.startDate > :startDate'
			))->setParameter('startDate', $startDate);

		return $qb->getQuery()->getArrayResult();
	}

	public function getOpenedTaskByEntity(?string $customerId = null, ?string $managerId = null, ?string $coordinatorId = null): mixed
	{
		if (empty($customerId) && empty($managerId) && empty($coordinatorId)) {
			return [];
		}
		$qb = $this->createQueryBuilder('t');
		$qb
			->innerJoin('t.project', 'p')
			->where($qb->expr()->eq('t.status', ':statusOpened'))
			->setParameters(new ArrayCollection([
				new Parameter('statusOpened', Task::STATUS_OPENED),
			]));

		if ($customerId) {
			$qb->andWhere($qb->expr()->eq('p.customer', ':customerId'))
				->setParameter('customerId', $customerId);
		}
		if ($managerId) {
			$qb->andWhere($qb->expr()->eq('t.projectManager', ':managerId'))
				->setParameter('managerId', $managerId);
		}
		if ($coordinatorId) {
			$qb->andWhere($qb->expr()->eq('t.projectCoordinator', ':coordinatorId'))
				->setParameter('coordinatorId', $coordinatorId);
		}

		return $qb->getQuery()->getArrayResult();
	}

	public function getCoordinatorTaskByEntity(?string $customerId = null, ?string $managerId = null): mixed
	{
		if (empty($customerId) && empty($managerId)) {
			return [];
		}
		$qb = $this->createQueryBuilder('t');
		$qb
			->innerJoin('t.project', 'p')
			->where($qb->expr()->eq('t.status', ':statusOpened'))
			->andWhere($qb->expr()->isNotNull('t.projectCoordinator'))
			->setParameters(new ArrayCollection([
				new Parameter('statusOpened', Task::STATUS_OPENED),
			]));

		if ($customerId) {
			$qb->andWhere($qb->expr()->eq('p.customer', ':customerId'))
				->setParameter('customerId', $customerId);
		}
		if ($managerId) {
			$qb->andWhere($qb->expr()->eq('t.projectManager', ':managerId'))
				->setParameter('managerId', $managerId);
		}

		return $qb->getQuery()->getArrayResult();
	}

	public function getTotalWorkingFilesByProject(string $projectId): mixed
	{
		$qb = $this->createQueryBuilder('t');
		$qb
			->select('SUM(t.workingFilesNumber) as sum')
			->where($qb->expr()->eq('t.project', ':projectId'))
			->setParameters(new ArrayCollection([
				new Parameter('projectId', $projectId),
			]));

		$result      = [];
		$queryResult = $qb->getQuery()->getArrayResult();
		if ($queryResult) {
			$result = array_shift($queryResult);
		}

		return $result;
	}

	public function getTotalWordsByProject(string $projectId): array
	{
		$qb = $this->createQueryBuilder('t');
		$qb->select('SUM(tcc.totalQuantity) as sum')
			->innerJoin('t.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCatCharges', 'tcc')
			->innerJoin('tcc.calculationUnit', 'unit')
			->innerJoin('t.project', 'pro')
			->where(
				$qb->expr()->andX(
					$qb->expr()->orX(
						$qb->expr()->eq('unit.symbol', ':unitSourceWord'),
						$qb->expr()->eq('unit.symbol', ':unitTargetWord')
					),
					$qb->expr()->eq('pro.id', ':projectId')
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('unitSourceWord', CalculationUnit::UNIT_SOURCE_WORD),
				new Parameter('unitTargetWord', CalculationUnit::UNIT_TARGET_WORD),
				new Parameter('projectId', $projectId),
			]));

		$result      = [];
		$queryResult = $qb->getQuery()->getResult();
		if ($queryResult) {
			$result = array_shift($queryResult);
		}

		return $result;
	}
}
