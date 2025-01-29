<?php

namespace App\Model\Utils;

use App\Model\Entity\Language;
use App\Model\Repository\LanguageRepository;

class LanguageHelper
{
	/**
	 * @var LanguageRepository
	 */
	private $repository;

	/**
	 * LanguageHelper constructor.
	 */
	public function __construct(
		LanguageRepository $repository
	) {
		$this->repository = $repository;
	}

	public function translateLanguageCode($language): string
	{
		// TODO; this should not be hardcoded, better to put it into database or reorganize languages
		$langTranslationTable = [
			'pa-PA' => 'pa-IN',
			'ku-TR' => 'ku-IQ',
			'kmr'   => 'ku-IQ',
			'ckb'   => 'ku-IQ',
			'sdh'   => 'ku-IQ',
			'AM-ES' => 'hy-AM-arevela',
			'AM-WE' => 'hy-AM-arevmda',
			'goyu'  => 'zh-TW',
			'cmn'   => 'zh-CN',
		];
		$language = str_replace('_', '-', $language);

		return strtr($language, $langTranslationTable);
	}

	/**
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findLanguage($language): ?Language
	{
		$language = static::translateLanguageCode($language);

		$language    = explode('-', $language);
		$language[0] = strtolower($language[0]);

		switch (strlen($language[0])) {
			case 2:
				return $this->repository->findOneByIso1($language[0]);
			case 3:
				$lang = $this->repository->findOneByIso3($language[0]);
				if (null === $lang) {
					$lang = $this->repository->findOneByIso2($language[0]);
				}

				return $lang;
			default:
				return null;
		}
	}
}
