<?php

namespace App\Apis\Shared\Util;

use App\Apis\Shared\Http\Validator\ApiDateIdConstraintValidator;
use App\Model\Entity\ContactPerson;
use App\Apis\Shared\Traits\UserResolver;

class UtilsService
{
	use UserResolver;

	public function stringFormattedDate($date, $initial = true)
	{
		$hour = $mins = $seconds = 0;
		if (!$initial) {
			$hour = 23;
			$mins = $seconds = 59;
		}
		try {
			return (new \DateTime($date))->setTime($hour, $mins, $seconds)->format('Y-m-d H:i:s');
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Return amount in proper format.
	 */
	public static function amountFormat($amount, $decimals = 2, $decPoint = '.', $thousandsSep = ',')
	{
		return number_format(floatval($amount), $decimals, $decPoint, $thousandsSep);
	}

	/**
	 * Return amount in number in proper format.
	 */
	public function amountNumberFormat($amount, $decimals = 2, $decPoint = '.', $thousandsSep = '')
	{
		return floatval(number_format($amount, $decimals, $decPoint, $thousandsSep));
	}

	/**
	 * Return array containing the dates for specific timeline period THIS_YEAR, LAST_YEAR, THIS_QUARTER_ LAST_QUARTER.
	 */
	public function arrayDateYearsOrQuarters(string $period): array
	{
		switch ($period) {
			case ApiDateIdConstraintValidator::DATE_THIS_YEAR:
				return [
					'startDate' => (new \DateTime('1st January this year'))->setTime(0, 0)->format('Y-m-d H:i:s'),
					'endDate' => (new \DateTime('now'))->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
				];
			case ApiDateIdConstraintValidator::DATE_LAST_YEAR:
				return [
					'startDate' => (new \DateTime('1st January last year'))->setTime(0, 0)->format('Y-m-d H:i:s'),
					'endDate' => (new \DateTime('last day of December last year'))->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
				];
			case ApiDateIdConstraintValidator::DATE_THIS_QUARTER:
				return $this->getThisOrPreviousQuarter();
			case ApiDateIdConstraintValidator::DATE_LAST_QUARTER:
				return $this->getThisOrPreviousQuarter(true);
		}

		return [];
	}

	/**
	 * Return array containing the dates for specific timeline period Month, Quarter and Year:.
	 */
	public function arrayDateTimeline(string $timeline): ?array
	{
		switch ($timeline) {
			case 'month':
				return [
					'startDate' => (new \DateTime('first day of this month'))->modify('-11 months')->setTime(0, 0)->format('Y-m-d H:i:s'),
					'endDate' => (new \DateTime('now'))->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
				];
			case 'quarter':
				$currentMonth = (new \DateTime('now'))->format('m');
				$currentYear = (new \DateTime('now'))->format('Y');
				$endMonth = 0 == $currentMonth % 3 ? $currentMonth : $currentMonth - ($currentMonth % 3);
				if ($endMonth <= 0) {
					$endMonth = 12;
					--$currentYear;
				}
				$days = cal_days_in_month(CAL_GREGORIAN, $endMonth, $currentYear);

				return [
					'startDate' => (new \DateTime("1-$endMonth-$currentYear"))->modify('+1 month')->modify('-24 months')->setTime(0, 0)->format('Y-m-d H:i:s'),
					'endDate' => (new \DateTime("$days-$endMonth-$currentYear"))->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
				];
			case 'year':
				return [
					'startDate' => (new \DateTime('1st January this year'))->modify('-3 year')->setTime(0, 0)->format('Y-m-d H:i:s'),
					'endDate' => (new \DateTime('now'))->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
				];
		}

		return null;
	}

	public function getThisOrPreviousQuarter(bool $previous = false): array
	{
		$date = new \DateTime('now');
		if ($previous) {
			$date->modify('-3 months');
		}
		$quarter = ceil($date->format('n') / 3);
		$start = new \DateTime();
		$start->setDate($date->format('Y'), ($quarter * 3) - 2, 1);
		$end = new \DateTime();
		$end->setDate($date->format('Y'), $quarter * 3, 1);
		$end->setDate($date->format('Y'), $quarter * 3, $end->format('t'));

		return [
			'startDate' => $start->setTime(0, 0)->format('Y-m-d H:i:s'),
			'endDate' => $end->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
		];
	}

	public function arrayDateComparative()
	{
		$currentMonth = (new \DateTime('now'))->format('m');
		$currentYear = (new \DateTime('now'))->format('Y');
		$startYear = $currentYear - 1;
		$endMonth = 0 == $currentMonth % 3 ? $currentMonth : $currentMonth - ($currentMonth % 3);
		if ($endMonth <= 0) {
			$endMonth = 12;
			--$currentYear;
			--$startYear;
		}
		$days = cal_days_in_month(CAL_GREGORIAN, $endMonth, $currentYear);

		return [
			'startDate' => (new \DateTime("01-01-$startYear"))->setTime(0, 0)->format('Y-m-d H:i:s'),
			'endDate' => (new \DateTime("$days-$endMonth-$currentYear"))->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
		];
	}

	/**
	 * Gets list of months between two dates.
	 */
	public function getMonthsListInRange(string $startDate, string $endDate, bool $unique = false): array
	{
		$result = [];
		$start = (new \DateTime("$startDate"))->modify('first day of this month');
		$end = (new \DateTime("$endDate"))->modify('last day of this month');
		$interval = \DateInterval::createFromDateString('1 month');
		$period = new \DatePeriod($start, $interval, $end);
		/** @var \DateTime $dt */
		foreach ($period as $dt) {
			$month = "{$dt->format('M')}";
			$year = "{$dt->format('Y')}";

			if (!$unique) {
				$result[] = "$month $year";
				continue;
			}
			if (!in_array("$month", $result)) {
				$result[] = "$month";
			}
		}

		return $result;
	}

	/**
	 * Gets list of years between two dates.
	 */
	public function getYearsListInRange(string $startDate, string $endDate): array
	{
		$result = [];
		$start = (new \DateTime("$startDate"))->modify('first day of this month');
		$end = (new \DateTime("$endDate"))->modify('first day of this month');
		$interval = \DateInterval::createFromDateString('1 year');
		$period = new \DatePeriod($start, $interval, $end);
		/** @var \DateTime $dt */
		foreach ($period as $dt) {
			$result[] = "{$dt->format('Y')}";
		}

		return $result;
	}

	/**
	 * Gets list of quarters between two dates.
	 */
	public function getQuartersListInRange(string $startDate, string $endDate, bool $unique = false): array
	{
		$result = [];
		$start = (new \DateTime("$startDate"))->modify('first day of this month');
		$end = (new \DateTime("$endDate"))->modify('first day of this month');
		$interval = \DateInterval::createFromDateString('3 months');
		$period = new \DatePeriod($start, $interval, $end);
		/** @var \DateTime $dt */
		foreach ($period as $dt) {
			$month = "{$dt->format('n')}";
			$year = "{$dt->format('Y')}";
			$quarter = ceil($month / 3);
			if (!$unique) {
				$result[] = "Q$quarter $year";
				continue;
			}
			if (!in_array("Q$quarter", $result)) {
				$result[] = "Q$quarter";
			}
		}

		return $result;
	}

	/**
	 * Gets list of quarters for the curent year.
	 */
	public function getQuartersListThisYear(): array
	{
		$result = [];
		$start = (new \DateTime('1st January this year'));
		$currentMonth = (new \DateTime('now'))->format('m');
		$currentYear = (new \DateTime('now'))->format('Y');
		$endMonth = 0 == $currentMonth % 3 ? $currentMonth : $currentMonth - ($currentMonth % 3);
		if ($endMonth <= 0) {
			$endMonth = 12;
			--$currentYear;
		}
		$days = cal_days_in_month(CAL_GREGORIAN, $endMonth, $currentYear);
		$end = (new \DateTime("$days-$endMonth-$currentYear"))->modify('first day of this month');
		if ($currentMonth < 3) {
			$start = $start->modify('-1 year');
		}

		$interval = \DateInterval::createFromDateString('3 months');
		$period = new \DatePeriod($start, $interval, $end);
		/** @var \DateTime $dt */
		foreach ($period as $dt) {
			$month = "{$dt->format('n')}";
			$quarter = ceil($month / 3);
			$result[] = "Q$quarter";
		}

		return $result;
	}

	public function getTimelineStatus($value)
	{
		$operation = floatval($value) / 3600;
		if ($operation < 0) {
			return 'Earlier';
		}
		if ($operation <= 1) {
			return 'OnTime';
		}

		return 'Late';
	}

	public function stringContains($search, $haystack)
	{
		if (str_contains($haystack, $search)) {
			return true;
		}

		return false;
	}

	/**
	 * @return array|string|string[]
	 */
	public static function stringToCamelCase($string, $capitalizeFirstCharacter = false)
	{
		$string = str_replace([' ', '&', ','], '-', $string);
		$str1 = str_replace('-', '', ucwords($string, '-'));
		$str = str_replace('_', '', ucwords($str1, '_'));

		if (!$capitalizeFirstCharacter) {
			$str = lcfirst($str);
		}

		return $str;
	}

	public function arrayKeysToCamel(array|string &$data)
	{
		if (!is_array($data) && is_string($data)) {
			$data = self::stringToCamelCase($data);

			return;
		}
		foreach ($data as $key => $datum) {
			if ($this->stringContains('_', $key) || $this->stringContains('-', $key)) {
				$newKey = self::stringToCamelCase($key);
				$data[$newKey] = $datum;
				unset($data[$key]);
			}
		}
	}

	public function stringEndsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if (0 == $length) {
			return true;
		}

		return substr($haystack, -$length) === $needle;
	}

	public function stringStartWith($haystack, $needle)
	{
		$length = strlen($needle);
		if (0 == $length) {
			return true;
		}

		return substr($haystack, 0, $length) === $needle;
	}

	/**
	 * @return mixed|string
	 */
	public function removeSubstringFromStart($subString, $text)
	{
		if (substr($text, 0, strlen($subString)) == $subString) {
			$text = substr($text, strlen($subString));
		}

		return $text;
	}

	/**
	 * @return mixed|string
	 */
	public function removeSubstringFromEnd($subString, $text)
	{
		if (substr($text, -strlen($subString)) == $subString) {
			$text = substr($text, 0, strlen($text) - strlen($subString));
		}

		return $text;
	}

	public function isJson($string)
	{
		json_decode($string);

		return JSON_ERROR_NONE == json_last_error();
	}

	/**
	 * @throws \Exception
	 */
	public static function getRange($start, $range): array
	{
		$now = new \DateTime();
		$now = $now->sub(new \DateInterval(sprintf('P%s', $start)));
		$end = clone $now;
		switch (substr($start, -1)) {
			case 'M':
				$now = $now->modify('first day of this month');
				$end = $end->modify('last day of this month');
				$end = $end->add(new \DateInterval(sprintf('P%s', $range)));
				break;
			case 'Y':
				$now = $now->setDate($now->format('Y'), 1, 1);
				$end = $end->add(new \DateInterval(sprintf('P%s', $range)))->sub(new \DateInterval('P1D'));
				break;
		}

		return [$now, $end];
	}

	public static function getFirstAndLastName(?string $name)
	{
		$firstName = '';
		$lastName = '';
		do {
			if (!$name) {
				break;
			}
			$splitted = explode(' ', $name);
			if (!count($splitted)) {
				break;
			}
			$firstName = $splitted[0];
			unset($splitted[0]);
			if (count($splitted)) {
				$lastName = implode(' ', $splitted);
			}
		} while (0);

		return [$firstName, $lastName];
	}

	public function getCurrentTimezone(): ?string
	{
		if (!$this->tokenStorage) {
			return null;
		}
		/** @var ContactPerson $user */
		$user = $this->tokenStorage->getToken()?->getUser();
		if ($user) {
			$timezone = $this->tokenStorage->getToken()?->getUser()?->getTimezone();
			if (!empty($user->getPreferences()) && isset($user->getPreferences()['timezone'])) {
				$timezone = $user->getPreferences()['timezone'];
			}

			return $timezone;
		}

		return null;
	}

	/**
	 * @return mixed
	 */
	public function getDateWithTimezone(?\DateTimeInterface $dateTime, string $format): ?string
	{
		if (!$dateTime) {
			return null;
		}
		$timezone = $this->getCurrentTimezone();
		if (!$timezone) {
			return $dateTime->format($format);
		}

		return $dateTime->setTimezone(new \DateTimeZone($timezone))->format($format);
	}

	public function getSecondsFromPTDate(?string $ptDate): int
	{
		if (empty($ptDate)) {
			return 0;
		}
		$interval = new \DateInterval($ptDate);

		return ($interval->h * 3600) + $interval->i * 60 + $interval->s;
	}
}
