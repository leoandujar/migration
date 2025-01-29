<?php
namespace App\Apis\Shared\Util;

use Yasumi\Holiday;
use Yasumi\Provider\ChristianHolidays;
use Yasumi\Provider\DateTimeZoneFactory;
use Yasumi\Provider\USA;

class HolidayProvider extends USA
{
	use ChristianHolidays;

	public function initialize(): void
	{
		parent::initialize();

		// Add Christmas observed: December 23rd
		$this->addHoliday(new Holiday(
			'christmasDayOberserved',
			[
				'en' => 'Christmas Day Observed',
			],
			new \DateTime("{$this->year}-12-23", DateTimeZoneFactory::getDateTimeZone($this->timezone)),
			$this->locale,
			Holiday::TYPE_OBSERVANCE
		));

		// Add Christmas Eve observed: December 24th
		$this->addHoliday($this->christmasEve($this->year, $this->timezone, $this->locale));

		// Add Thanksgiving observed
		$this->calculateThanksgivingDayObserved();
	}

	private function calculateThanksgivingDayObserved(): void
	{
		if ($this->year >= 1863) {
			$thanksgivingDay =  new \DateTime("fourth thursday of november $this->year", DateTimeZoneFactory::getDateTimeZone($this->timezone));

			$this->addHoliday(new Holiday(
				'thanksgivingDayObserved',
				[
					'en' => 'Thanksgiving Day Observed',
				],
				$thanksgivingDay->modify('+1 day'), // next day
				$this->locale,
				Holiday::TYPE_OBSERVANCE
			));
		}
	}
}
