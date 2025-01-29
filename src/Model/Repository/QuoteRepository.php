<?php

namespace App\Model\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use App\Model\Entity\Quote;
use App\Model\Entity\QuoteLanguageCombination;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuoteRepository extends EntityRepository
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
		$class = $em->getClassMetadata(Quote::class);
		parent::__construct($em, $class);
	}

	public function getSearchQuote(array $params): mixed
	{
		$qb = $this->createQueryBuilder('q');
		$qb->select('q')
			->setFirstResult($params['start'])
			->setMaxResults($params['per_page']);

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("q.{$params['sort_by']}", $params['sort_order']);
		}
		$this->quoteSearchFilter($params, $qb);

		return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
	}

	public function getCountSearchQuote(array $params): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('q');
		$qb->select('COUNT(q.id)');

		$this->quoteSearchFilter($params, $qb);

		return $qb->getQuery()->getSingleScalarResult();
	}

	private function quoteSearchFilter(array $params, &$qb): void
	{
		$initDate = '1970-01-01 00:00:00';
		$nowDate = (new \DateTime('now'))->format('Y-m-d H:i:s');
		if (!empty($params['status'])) {
			if (in_array('REQUESTED', $params['status'])) {
				array_push($params['status'], 'PENDING');
			}
			$qb
				->andWhere($qb->expr()->in('q.status', ':statuses'))
				->setParameter('statuses', $params['status']);
		}
		if (!empty($params['customer_project_number'])) {
			$qb
				->andWhere($qb->expr()->eq('q.customerProjectNumber', ':refNumber'))
				->setParameter('refNumber', $params['customer_project_number']);
		}
		if (!empty($params['search']) || !empty($params['contact_person_id'])) {
			$qb
				->leftJoin('q.customerContactPerson', 'custp')
				->leftJoin('custp.contactPerson', 'contp');

			if (!empty($params['search'])) {
				$qb
					->andWhere(
						$qb->expr()->orX(
							$qb->expr()->like('LOWER(q.idNumber)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(q.customerProjectNumber)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(q.name)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(contp.name)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(contp.nameNormalized)', 'LOWER(:search)')
						)
					)
					->setParameter('search', "%{$params['search']}%");
			}
			if (!empty($params['contact_person_id'])) {
				$qb

					->andWhere(
						$qb->expr()->in('contp.id', ':contactPersonList')
					)
					->setParameter('contactPersonList', $params['contact_person_id']);
			}
		}
		if (!empty($params['requested_on'][0]) || !empty($params['requested_on'][1])) {
			$startDate = $params['requested_on'][0] ?? $initDate;
			$endDate = $params['requested_on'][1] ?? $nowDate;
			$endDate = (new \DateTime($endDate))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
			$qb
				->andWhere(
					$qb->expr()->between('q.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $startDate)
				->setParameter('endDate', $endDate);
		}
		if (!empty($params['deadline'][0]) || !empty($params['deadline'][1])) {
			$deadlineStartDate = $params['deadline'][0] ?? $initDate;
			$deadlineEndDate = $params['deadline'][1] ?? $nowDate;
			$deadlineEndDate = (new \DateTime($deadlineEndDate))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
			$qb
				->andWhere(
					$qb->expr()->between('q.deadline', ':deadlineStartDate', ':deadlineEndDate')
				)
				->setParameter('deadlineStartDate', $deadlineStartDate)
				->setParameter('deadlineEndDate', $deadlineEndDate);
		}
		if (!empty($params['customer_id'])) {
			$qb
				->andWhere(
					$qb->expr()->in('q.customer', ':customerId')
				)
				->setParameter('customerId', $params['customer_id']);
		}
		if (!empty($params['target_languages'])) {
			$qb
				->innerJoin('q.languagesCombinations', 'langCombi')
				->andWhere(
					$qb->expr()->in('langCombi.targetLanguage', ':targetLanguages')
				)
				->setParameter('targetLanguages', $params['target_languages']);
		}
		if (!empty($params['services'])) {
			$qb
				->andWhere(
					$qb->expr()->in('q.service', ':services')
				)
				->setParameter('services', $params['services']);
		}
		if (!empty($params['source_languages'])) {
			$subQueryBuilder = $this->em->createQueryBuilder();
			$subQuery = $subQueryBuilder
				->select('q1.id')
				->from(QuoteLanguageCombination::class, 'qlc')
				->join('qlc.quote', 'q1')
				->where($subQueryBuilder->expr()->in('qlc.sourceLanguage', ':sourceLanguages'))
				->getDQL();

			$qb
			->andWhere(
				$qb->expr()->in('q.id', $subQuery)
			)
			->setParameter('sourceLanguages', $params['source_languages']);
		}
	}

	public function getRequestedByEntity(?string $customerId = null, ?string $managerId = null, ?string $coordinatorId = null): mixed
	{
		if (empty($customerId) && empty($managerId) && empty($coordinatorId)) {
			return [];
		}
		$qb = $this->createQueryBuilder('q');
		$qb
			->innerJoin('q.project', 'p')
			->where($qb->expr()->eq('q.status', ':statusRequested'))
			->setParameters(new ArrayCollection([
				new Parameter('statusRequested', Quote::STATUS_REQUESTED),
			]));

		if ($customerId) {
			$qb->andWhere($qb->expr()->eq('p.customer', ':customerId'))
				->setParameter('customerId', $customerId);
		}
		if ($managerId) {
			$qb->andWhere($qb->expr()->eq('p.projectManager', ':managerId'))
				->setParameter('managerId', $managerId);
		}
		if ($coordinatorId) {
			$qb->andWhere($qb->expr()->eq('p.projectCoordinator', ':coordinatorId'))
				->setParameter('coordinatorId', $coordinatorId);
		}

		return $qb->getQuery()->getArrayResult();
	}
}
