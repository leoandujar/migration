<?php

namespace App\Model\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\QualityCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class QualityCategoryRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(QualityCategory::class);
		parent::__construct($em, $class);
	}

	public function getParentCategories(?string $type): ?array
	{
		$q = $this->createQueryBuilder('dqaC');
		$q
			->select('dqaC.id', 'dqaC.name')
			->where(
				$q->expr()->isNull('dqaC.parentCategory')
			);

		if (!empty($type)) {
			$q
				->andWhere($q->expr()->eq('dqaC.type', ':type'))
				->setParameter('type', $type);
		}

		return $q->getQuery()->getArrayResult();
	}

	public function getChildCategories(int $categoryId): ?array
	{
		$q = $this->createQueryBuilder('dqaC');

		$q->select(
			'dqaC',
			'an'
		)
			->leftJoin('dqaC.qualityAnswers', 'an')
			->where(
				$q->expr()->eq('dqaC.parentCategory', ':dqaCategoryId')
			)
			->setParameters(new ArrayCollection([
				new Parameter('dqaCategoryId', $categoryId),
			]));

		return $q->getQuery()->getArrayResult();
	}
}
