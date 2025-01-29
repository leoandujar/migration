<?php

namespace App\Model\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\APFormSubmission;
use Doctrine\ORM\EntityManagerInterface;

class APFormSubmissionRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(APFormSubmission::class);
		parent::__construct($em, $class);
	}

	public function getSearch(array $params): mixed
	{
		$qb = $this->createQueryBuilder('fs');
		$qb->select('fs')
			->innerJoin('fs.apForm', 'f')
			->orderBy('fs.updatedAt', 'DESC')
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("fs.{$params['sort_by']}", $params['sort_order']);
		}
		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}

	private function searchFilter(array $params, &$qb): void
	{
		if (!empty($params['status'])) {
			$qb
				->andWhere($qb->expr()->in('fs.status', ':statuses'))
				->setParameter('statuses', $params['status']);
		}

		if (!empty($params['search'])) {
			$qb
				->andWhere($qb->expr()->like('LOWER(f.name)', 'LOWER(:name)'))
				->setParameter('name', "%{$params['search']}%");
		}

		if (!empty($params['form_id'])) {
			$qb
				->andWhere($qb->expr()->eq('f.id', ':formId'))
				->setParameter('formId', $params['form_id']);
		}

		if (!empty($params['related'])) {
			$userId = $params['userId'];
			$orX = $qb->expr()->orX(
				$qb->expr()->eq('fs.submittedBy', ':userId'),
				$qb->expr()->eq('fs.owner', ':userId'),
			);
			$orX->add('CONTAINS(f.approvers, :userArray) = true');
			$orX->add('CONTAINS(fs.collaborators, :userArray) = true');

			$qb->setParameter('userId', $userId);
			$qb->setParameter('userArray', '["'.$userId.'"]');

			$qb->andWhere($orX);
		}

		if (!empty($params['appoved_by'])) {
			$qb
				->andWhere($qb->expr()->orX(
					$qb->expr()->eq('fs.approvedBy', ':appovedBy'),
					$qb->expr()->eq('fs.status', ':status')
				))
				->setParameter('status', 'pending')
				->setParameter('appovedBy', $params['appoved_by']);
		}

		if (!empty($params['approver_ids'])) {
			$orX = $qb->expr()->orX();
			foreach ($params['approver_ids'] as $key => $approver) {
				$orX->add("CONTAINS(f.approvers, :approver$key) = true");
				$qb->setParameter("approver$key", '["'.$approver.'"]');
			}
			$qb->andWhere($orX);
		}

		if (!empty($params['submitted_by'])) {
			$qb
				->andWhere(
					$qb->expr()->eq('fs.submittedBy', ':submittedBy')
				)
				->setParameter('submittedBy', $params['submitted_by']);
		}
	}

	public function getCountRows(array $params): int
	{
		$qb = $this->createQueryBuilder('fs');
		$qb->select('COUNT(fs.id)')
			->innerJoin('fs.apForm', 'f');

		$this->searchFilter($params, $qb);

		return $qb->getQuery()->getSingleScalarResult() ?? 0;
	}
}
