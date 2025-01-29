<?php

namespace App\Model\Repository;

use App\Model\Entity\FetchQueue;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchQueueRepository extends EntityRepository
{
	public const MAX_RESULTS = 50;

	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(FetchQueue::class);
		parent::__construct($em, $class);
	}

	public function findByExternalId(string $entity, string $externalId): ?FetchQueue
	{
		return $this->findOneBy(['entity' => $entity, 'externalId' => $externalId]);
	}

	public function findToProcess(string $entity, string $source, int $limit = null): array
	{
		if (null === $limit) {
			$limit = self::MAX_RESULTS;
		}
		$return = $this->createQueryBuilder('f')
			->where('f.entity = :entity')->setParameter('entity', $entity)
			->andWhere('f.source = :source')->setParameter('source', $source)
			->orderBy('f.date', 'ASC')
			->addOrderBy('length(f.externalId)', 'ASC')
			->addOrderBy('f.externalId', 'ASC');
		if ($limit > 0) {
			$return->setMaxResults($limit);
		}

		return $return
			->getQuery()
			->getArrayResult();
	}

	public function cleanUp($cleanData, OutputInterface $output): void
	{
		foreach ($cleanData as $name => $entries) {
			$output->write($name.': ');
			foreach ($entries as $entry) {
				$fq = new FetchQueue();
				$fq->setDate($entry['date'])
					->setSource($entry['source'])
					->setEntity($entry['entity'])
					->setExternalId($entry['id']);
				$this->getEntityManager()->persist($fq);
			}
			$output->writeln(count($entries));
		}
		$this->getEntityManager()->flush();
	}
}
