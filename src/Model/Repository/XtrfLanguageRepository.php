<?php

namespace App\Model\Repository;

use App\Model\Entity\Project;
use App\Model\Entity\ProjectLanguageCombination;
use App\Model\Entity\XtrfLanguage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class XtrfLanguageRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(XtrfLanguage::class);
		parent::__construct($em, $class);
	}

	public function findOneByIso3(string $iso3): ?XtrfLanguage
	{
		return $this->findOneBy(['iso3' => $iso3]);
	}

	public function findOneByIso2t(string $iso2t): ?XtrfLanguage
	{
		return $this->findOneBy(['iso2t' => $iso2t]);
	}

	public function findOneByIso2b(string $iso2b): ?XtrfLanguage
	{
		return $this->findOneBy(['iso2b' => $iso2b]);
	}

	public function findOneByIso2(string $iso2): ?XtrfLanguage
	{
		return $this->createQueryBuilder('l')
			->orWhere('l.iso2b = :iso2b')
			->orWhere('l.iso2t = :iso2t')
			->setParameter('iso2b', $iso2)
			->setParameter('iso2t', $iso2)
			->getQuery()
			->getOneOrNullResult()
		;
	}

	public function findOneByIso1(string $iso1): ?XtrfLanguage
	{
		return $this->findOneBy(['iso1' => $iso1]);
	}

	public function findByIds(array $ids): mixed
	{
		$qb = $this->createQueryBuilder('l');
		$qb->where($qb->expr()->in('l.id', ':ids'))
			->setParameter('ids', $ids);

		return $qb->getQuery()->getResult();
	}

	public function findByMapping(string $value): ?XtrfLanguage
	{
		$qb = $this->createQueryBuilder('l');
		$qb->where(
			$qb->expr()->like('LOWER(l.mapping)', 'LOWER(:value)')
		)
			->setMaxResults(1)
			->setParameters(new ArrayCollection([
				new Parameter('value', "%$value%"),
			]));
		$sqlResult = $qb->getQuery()->getOneOrNullResult();
		if (is_array($sqlResult) && count($sqlResult) > 0) {
			return array_shift($sqlResult);
		}

		return $sqlResult;
	}

	public function getTopTargetLanguages(string $customerId, \DateTime $date, int $limit = 5): ArrayCollection
	{
		$qb = $this->createQueryBuilder('l');

		$qb->select('l')
		->innerJoin(ProjectLanguageCombination::class, 'plc', 'WITH', 'plc.targetLanguage = l.id')
		->leftJoin(Project::class, 'p', 'WITH', 'p.id = plc.project')
		->where('p.createdOn >= :date')
		->andWhere('plc.targetLanguage IS NOT NULL')
		->andWhere('p.customer = :customer')
		->groupBy('l.id', 'l.name')
		->orderBy('count(l.id)', 'DESC')
		->addOrderBy('l.name', 'ASC')
		->setMaxResults($limit)
		->setParameter('date', $date)
		->setParameter('customer', $customerId);

		$result = $qb->getQuery()->getResult();

		return new ArrayCollection($result);
	}
}
