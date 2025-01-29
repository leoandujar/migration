<?php

namespace App\Model\Repository;

use App\Model\Entity\BlCall;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class BlCallRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(BlCall::class);
		parent::__construct($entityManager, $class);
	}

	public function getCalls(array $params): ?array
	{
		$q = $this->createQueryBuilder('c')->orderBy('c.id', 'DESC');
		$q->where(
			$q->expr()->gt('c.customerAmount', ':amount')
		)
			->setParameter('amount', 0);

		if (!empty($params['customer_id'])) {
			$q
				->andWhere(
					$q->expr()->in('c.blCustomer', ':customerId')
				)
				->setParameter('customerId', $params['customer_id']);
		}

		if (isset($params['start_date'])) {
			$q->andWhere(
				$q->expr()->between('c.startDate', ':startDate', ':endDate')
			)
				->setParameter('startDate', $params['start_date'][0])
				->setParameter('endDate', $params['start_date'][1]);
		}

		return $q->getQuery()->getResult();
	}

	public function getCallsByCustomer(int $customer, array $params): ?array
	{
		$q = $this->createQueryBuilder('c');
		$q->select(
			'IDENTITY(tl.xtrfLanguage) as languageId,
			 c.blAmount as blAmount,
             c.blReferenceId as referenceId,
             c.interpreterName as interpreter,
             bc.name as requester,
             c.startDate as date,
             tl.englishName as language,
             ct.name as type,
             c.customerDuration as duration,
             c.durationHours as durationHours,
             c.durationMinutes as durationMinutes,
             c.durationSeconds as durationSeconds,
			 c.customerAmount as amount,
			 c.fromNumber as fromNumber,
			 c.thirdPartyDuration as thirdPartyDuration,
			 c.additional as additional'
		)
			->innerJoin('c.blTargetLanguage', 'tl')
			->innerJoin('c.blCommunicationType', 'ct')
			->innerJoin('c.blContact', 'bc')
			->orderBy('c.blCallId', 'ASC');

		$q->where(
			$q->expr()->eq('c.blCustomer', ':id')
		)
			->setParameter('id', $customer);

		$q->andWhere(
			$q->expr()->gt('c.customerAmount', ':amount')
		)
			->setParameter('amount', 0);

		if (isset($params['start_date'])) {
			$q->andWhere(
				$q->expr()->between('c.startDate', ':startDate', ':endDate')
			)
				->setParameter('startDate', $params['start_date'][0])
				->setParameter('endDate', $params['start_date'][1]);
		}

		return $q->getQuery()->getResult();
	}

	public function getCallsTotals(array $params): ?array
	{
		$q = $this->createQueryBuilder('c');
		$q->select(
			'SUM(c.customerAmount) as amount,
			 SUM(c.customerDuration) as duration'
		);

		$q->where(
			$q->expr()->gt('c.customerAmount', ':amount')
		)
			->setParameter('amount', 0);

		if (!empty($params['customer_id'])) {
			$q
				->andWhere(
					$q->expr()->in('c.blCustomer', ':customerId')
				)
				->setParameter('customerId', $params['customer_id']);
		}
		if (isset($params['start_date'])) {
			$q->andWhere(
				$q->expr()->between('c.startDate', ':startDate', ':endDate')
			)
				->setParameter('startDate', $params['start_date'][0])
				->setParameter('endDate', $params['start_date'][1]);
		}

		return $q->getQuery()->getOneOrNullResult();
	}

	public function getCallsTotalsByLanguage(int $customer, array $params): ?array
	{
		$q = $this->createQueryBuilder('c');
		$q->select(
			'IDENTITY(tl.xtrfLanguage) as languageId,
			 tl.englishName as language,
			 SUM(c.customerAmount) as amount,
			 SUM(c.customerDuration) as duration,
			 SUM(c.blAmount) as blAmount',
		)
			->innerJoin('c.blTargetLanguage', 'tl')
			->groupBy('tl.id');

		$q->where(
			$q->expr()->eq('c.blCustomer', ':id')
		)
			->setParameter('id', $customer);

		if (isset($params['between'])) {
			$q->andWhere(
				$q->expr()->between('c.startDate', ':startDate', ':endDate')
			)
				->setParameter('startDate', $params['between'][0])
				->setParameter('endDate', $params['between'][1]);
		}

		return $q->getQuery()->getResult();
	}
}
