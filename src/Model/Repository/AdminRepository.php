<?php

namespace App\Model\Repository;

use App\Model\Entity\Customer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class AdminRepository
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * DashboardRepository constructor.
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->em = $entityManager;
	}

	public function getCustomersByName(string $partialName, int $limit): ?array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'cust.id',
			'cust.name'
		)
			->from(Customer::class, 'cust')
			->where(
				$q->expr()->andX(
					$q->expr()->eq('cust.status', ':status'),
					$q->expr()->like('LOWER(cust.name)', 'LOWER(:partialName)')
				)
			)
			->orderBy('cust.name')
			->setParameters(new ArrayCollection([
					new Parameter('partialName', "%$partialName%"),
					new Parameter('status', Customer::STATUS_ACTIVE),
				]))
			->setMaxResults($limit);

		return $q->getQuery()->getArrayResult();
	}

	public function getCustomers(): ?array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'cust.id',
			'cust.name'
		)
			->from(Customer::class, 'cust')
			->where(
				$q->expr()->eq('cust.status', ':status')
			)
			->orderBy('cust.name', 'ASC')
			->setParameters(new ArrayCollection([
				new Parameter('status', Customer::STATUS_ACTIVE),
				]));

		return $q->getQuery()->getArrayResult();
	}
}
