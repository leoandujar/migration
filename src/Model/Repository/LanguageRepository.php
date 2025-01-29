<?php

namespace App\Model\Repository;

use App\Model\Entity\Language;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class LanguageRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(Language::class);
		parent::__construct($em, $class);
	}

	public function findOneByIso3(string $iso3): ?Language
	{
		return $this->findOneBy(['iso3' => $iso3]);
	}

	public function findOneByIso2t(string $iso2t): ?Language
	{
		return $this->findOneBy(['iso2t' => $iso2t]);
	}

	public function findOneByIso2b(string $iso2b): ?Language
	{
		return $this->findOneBy(['iso2b' => $iso2b]);
	}

	/**
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findOneByIso2(string $iso2): ?Language
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

	public function findOneByIso1(string $iso1): ?Language
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
}
