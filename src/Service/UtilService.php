<?php

namespace App\Service;

class UtilService
{
	public const FROM_TYPE_NUMERIC_DATE = 1; // -1d or +5d...same with M(month) or Y(year)

	public const RETURN_FORMAT_DATETIME = 'datetime';
	public const RETURN_FORMAT_TIMESTAMP = 'timestamp';

	public function generateRandomString($length = 10): string
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; ++$i) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	public function getDateByFormat(
		$value,
		int $type = self::FROM_TYPE_NUMERIC_DATE,
		string $returnFormat = self::RETURN_FORMAT_DATETIME
	): null|int|\DateTimeInterface {
		try {
			/** @var \DateTimeInterface $date */
			$date = null;

			if (!str_contains(strtolower($value), 'day') && !str_contains($value, 'days') && str_contains($value, 'd')) {
				$value = str_replace('d', ' days', strtolower($value));
			} elseif (!str_contains(strtolower($value), 'month') && !str_contains($value, 'months') && str_contains($value, 'y')) {
				$value = str_replace('m', ' months', strtolower($value));
			} elseif (!str_contains(strtolower($value), 'week') && !str_contains($value, 'weeks') && str_contains($value, 'w')) {
				$value = str_replace('w', ' week', strtolower($value));
			} elseif (!str_contains(strtolower($value), 'year') && !str_contains($value, 'years') && str_contains($value, 'y')) {
				$value = str_replace('y', ' years', strtolower($value));
			}
			switch ($type) {
				case self::FROM_TYPE_NUMERIC_DATE:
					$date = (new \DateTime('now'))->modify($value);
					break;
			}
			if ($date && self::RETURN_FORMAT_TIMESTAMP === $returnFormat) {
				return $date->getTimestamp() * 1000;
			}

			return $date;
		} catch (\Throwable $thr) {
			throw $thr;
		}
	}
}
