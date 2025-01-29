<?php

namespace App\Model\Repository;

use App\Model\Entity\XtrfLanguage;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class LanguageTagRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(XtrfLanguage::class);
		parent::__construct($em, $class);
	}

	public function findBySymbol(string $value): XtrfLanguage
	{
		return $this->findOneBy(['symbol' => $value]);
	}

	public function findByExternalIso1(string $value): ?XtrfLanguage
	{
		return $this->findOneBy(['langiso' => $value]);
	}

	public function findByExternalIso2(string $value): ?XtrfLanguage
	{
		return $this->findOneBy(['langiso3' => $value]);
	}

	/**
	 * @return XtrfLanguage[]
	 */
	public function findByLanguage(string $value): array
	{
		return $this->findBy(['languageCode' => $value]);
	}

	/**
	 * @return XtrfLanguage[]
	 */
	public function findByCountry(string $value): array
	{
		return $this->findBy(['countryCode' => $value]);
	}

	/**
	 * @return XtrfLanguage[]
	 */
	public function findByScript(string $value): array
	{
		return $this->findBy(['script' => $value]);
	}

	/**
	 * @return XtrfLanguage[]
	 */
	public function findByLanguageCountry(string $language, ?string $country): array
	{
		return $this->findBy([
			'languageCode' => $language,
			'countryCode'  => $country,
		]);
	}

	/**
	 * @return XtrfLanguage[]
	 */
	public function findByLanguageCountryScript(string $language, ?string $country, ?string $script): array
	{
		return $this->findBy([
			'languageCode' => $language,
			'countryCode'  => $country,
			'script'       => $script,
		]);
	}
}
