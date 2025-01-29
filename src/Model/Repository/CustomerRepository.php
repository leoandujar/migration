<?php

namespace App\Model\Repository;

use App\Model\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class CustomerRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Customer::class);
		parent::__construct($em, $class);
	}

	public function findByName(string $name): ?Customer
	{
		$q = $this->createQueryBuilder('cust');
		$q->where(
			$q->expr()->eq('LOWER(cust.fullName)', 'LOWER(:name)')
		)
			->setParameter('name', $name);

		return $q->getQuery()->getOneOrNullResult();
	}

	public function getCustomers($filters): ?array
	{
		$q = $this->createQueryBuilder('cust');
		$q->select(
			'cust.id',
			'cust.name',
			'cust.categoryGroups',
			's.id as settingsId',
			'blCust.id as bl',
			'cust.lastModificationDate',
			'cust.status',
			'cust.lastLoginDate',
			'cust.lastFailedLoginDate',
			'cust.lastProjectDate',
			'cust.lastQuoteDate',
			'cust.numberOfProjects',
			'cust.numberOfQuotes',
		)
			->leftJoin('cust.settings', 's')
			->where($q->expr()->eq('cust.status', ':status'))
			->orderBy('cust.name')
			->setMaxResults($filters['limit']);

		if (isset($filters['onboarded'])) {
			$filters['onboarded'] ?
				$q->andWhere($q->expr()->isNotNull('s.id')) :
				$q->andWhere($q->expr()->isNull('s.id'));
		}
		if ($filters['blOnly']) {
			$q->innerJoin('cust.blCustomer', 'blCust');
		} else {
			$q->leftJoin('cust.blCustomer', 'blCust');
		}

		$q->setParameter('status', Customer::STATUS_ACTIVE);
		if ($filters['partialName']) {
			$q->andWhere(
				$q->expr()->like('LOWER(cust.name)', 'LOWER(:partialName)')
			);
			$partialName = $filters['partialName'];
			$q->setParameter('partialName', "%$partialName%");
		}

		return $q->getQuery()->getArrayResult();
	}

	private function buildCPTemplateQuery(): QueryBuilder
	{
		$q = $this->createQueryBuilder('cust');

		$q->select('cust.id', 'contactPerson.id as contactPersonId', 'cpTemplates.id as cpTemplateId')
		->innerJoin('cust.contactPersons', 'custPerson')
		->innerJoin('custPerson.contactPerson', 'contactPerson')
		->innerJoin('contactPerson.cpTemplates', 'cpTemplates');

		return $q;
	}

	public function getCPTemplates(): array
	{
		$q = $this->buildCPTemplateQuery();

		return $q->getQuery()->getArrayResult();
	}

	public function getCPTemplatesByCustId($filters): array
	{
		$q = $this->buildCPTemplateQuery();

		$q->where($q->expr()->eq('cust.id', ':customerId'))
		->setParameter('customerId', $filters['customerId']);

		return $q->getQuery()->getArrayResult();
	}
}
