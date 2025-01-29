<?php

namespace App\Model\Repository;

use App\Model\Entity\CustomFieldConfiguration;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldConfigurationRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(CustomFieldConfiguration::class);
		parent::__construct($em, $class);
	}

	public function schema(string $scope = 'PROJECT'): array
	{
		$q = $this->createQueryBuilder('cfc');
		$q
			->select(
				'cfc.key as key,
				cfc.name as name,
                cfc.selectionPossibleValues as options,
                cfc.type,
                cfc.description',
			)
			->where('JSONB_LIKE(cfc.fieldsNames, :key) = true')
			->setParameter('key', "%$scope%")
		;

		return $q->getQuery()->getResult();
	}
}
