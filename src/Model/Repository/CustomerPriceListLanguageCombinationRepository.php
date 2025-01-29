<?php

namespace App\Model\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\CustomerPriceListLanguageCombination;

class CustomerPriceListLanguageCombinationRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(CustomerPriceListLanguageCombination::class);
		parent::__construct($entityManager, $class);
	}

	public function getPriceLangCombination($sourceLanguage, $targetLanguage, $customerId): ?CustomerPriceListLanguageCombination
	{
		$q = $this->createQueryBuilder('cpllc');
		$q->select('cpllc')
			->leftJoin('cpllc.customerPriceList', 'priceList')
			->leftJoin('priceList.priceProfile', 'priceProfile')
			->where($q->expr()->andX(
				$q->expr()->in('cpllc.sourceLanguage', ':sourceLanguage'),
				$q->expr()->in('cpllc.targetLanguage', ':targetLanguage'),
				$q->expr()->in('priceProfile.customer', ':customerId'),
				$q->expr()->eq('priceProfile.active', 'true'),
                $q->expr()->eq('priceProfile.isDefault', 'true'),
			))
			->setParameters(new ArrayCollection([
				new Parameter('sourceLanguage', $sourceLanguage),
				new Parameter('targetLanguage', $targetLanguage),
				new Parameter('customerId', $customerId),
			]));

		return $q->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
	}
}
