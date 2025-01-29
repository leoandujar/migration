<?php

namespace App\Model\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Entity\ProviderInvoice;
use Doctrine\ORM\EntityManagerInterface;

class ProviderInvoiceRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(ProviderInvoice::class);
		parent::__construct($em, $class);
	}

	public function getSearchInvoices(array $params): mixed
	{
		$qb = $this->createQueryBuilder('inv');
		$qb->select('inv');
		if (!empty($params['start']) && !empty($params['per_page'])) {
			$qb
				->setFirstResult($params['start'])
				->setMaxResults($params['per_page']);
		}

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("inv.{$params['sort_by']}", $params['sort_order']);
		}

		$this->invoiceSearchFilter($params, $qb);

		return $qb->getQuery()->getResult();
	}

	public function getCountSearchInvoices(array $params): bool|float|int|string|null
	{
		$qb = $this->createQueryBuilder('inv');
		$qb->select('COUNT(inv.id)');

		$this->invoiceSearchFilter($params, $qb);

		return $qb->getQuery()->getSingleScalarResult();
	}

	private function invoiceSearchFilter(array $params, &$qb): void
	{
		$initDate = '1970-01-01 00:00:00';
		$nowDate = (new \DateTime('now'))->format('Y-m-d H:i:s');
		if (!empty($params['internal_status'])) {
			$qb
				->andWhere($qb->expr()->in('inv.state', ':statuses'))
				->setParameter('statuses', $params['internal_status']);
		}
		if (!empty($params['payment_status'])) {
			$qb
				->andWhere($qb->expr()->in('inv.paymentState', ':paymentStatuses'))
				->setParameter('paymentStatuses', $params['payment_status']);
		}
		if (!empty($params['status'])) {
			$qb
				->andWhere($qb->expr()->eq('inv.state', ':status'))
				->setParameter('status', $params['status']);
		}
		if (!empty($params['search']) || !empty($params['contact_person_id'])) {
			$qb
				->leftJoin('inv.accountencyPerson', 'accp')
				->leftJoin('accp.provider', 'prov')
				->leftJoin('accp.accountencyPerson', 'contp');

			if (!empty($params['search'])) {
				$qb
					->andWhere(
						$qb->expr()->orX(
							$qb->expr()->like('LOWER(inv.finalNumber)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(inv.draftNumber)', 'LOWER(:search)'),
							$qb->expr()->like('LOWER(prov.name)', 'LOWER(:search)')
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
		if (!empty($params['provider_id'])) {
			$qb
				->andWhere(
					$qb->expr()->in('inv.provider', ':providerId')
				)
				->setParameter('providerId', $params['provider_id']);
		}
		if (!empty($params['final_date'][0]) || !empty($params['final_date'][1])) {
			$startDate = $params['final_date'][0] ?? $initDate;
			$endDate = $params['final_date'][1] ?? $nowDate;
			$qb
				->andWhere(
					$qb->expr()->between('inv.finalDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $startDate)
				->setParameter('endDate', $endDate);
		}
		if (!empty($params['due_date'][0]) || !empty($params['due_date'][1])) {
			$dueDateStartDate = $params['due_date'][0] ?? $initDate;
			$dueDateEndDate = $params['due_date'][1] ?? $nowDate;
			$qb
				->andWhere(
					$qb->expr()->between('inv.requiredPaymentDate', ':dueDateStartDate', ':dueDateEndDate')
				)
				->setParameter('dueDateStartDate', $dueDateStartDate)
				->setParameter('dueDateEndDate', $dueDateEndDate);
		}
	}

	public function getSearchInvoicesIds(array $params): array
	{
		$qb = $this->createQueryBuilder('inv');
		$qb->select('inv.id');
		if (!empty($params['start']) && !empty($params['per_page'])) {
			$qb
				->setFirstResult($params['start'])
				->setMaxResults($params['per_page']);
		}

		if (isset($params['sort_by']) && isset($params['sort_order'])) {
			$qb->orderBy("inv.{$params['sort_by']}", $params['sort_order']);
		}

		$this->invoiceSearchFilter($params, $qb);

		$result = [];
		$queryResult = $qb->getQuery()->getArrayResult();
		foreach ($queryResult as $item) {
			$result[] = $item['id'];
		}

		return $result;
	}
}
