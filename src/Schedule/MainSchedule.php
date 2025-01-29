<?php

namespace App\Schedule;

use App\Message\LogHelloMessage;
use App\Message\ConnectorsBoostlingoFetchMessage;
use App\Message\ConnectorsHubspotFetchMessage;
use App\Message\TimescaleMessage;
use App\Message\XtmProcessMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
readonly class MainSchedule implements ScheduleProviderInterface
{
	private ParameterBagInterface $parameterBag;

	public function __construct(
		private CacheInterface $cache,
		ParameterBagInterface $parameterBag,
	) {
		$this->parameterBag = $parameterBag;
	}

	public function getSchedule(): Schedule
	{
		if (!filter_var($this->parameterBag->get('app.messenger_enabled'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
			return new Schedule();
		}

		return (new Schedule())
			->add(
				RecurringMessage::every('5 minutes', new LogHelloMessage(60)),
				//				RecurringMessage::cron('*/20 * * * *', new XtmProcessMessage(limit: 200)),
				RecurringMessage::every('1 hour', new ConnectorsBoostlingoFetchMessage(entity: 'call', since: '2 hours')),
				// RecurringMessage::every('1 hour', new ConnectorsBoostlingoFetchMessage(entity: 'customer')),
				RecurringMessage::cron('0 */1 * * *', new ConnectorsBoostlingoFetchMessage(entity: 'call', since: '1 month')),
				RecurringMessage::cron('20 16 * * *', new ConnectorsBoostlingoFetchMessage(entity:'invoices_calls')),
				//				RecurringMessage::every('1 day', new TimescaleMessage()),
				//				RecurringMessage::cron('0 * * * *', new ConnectorsHubspotFetchMessage(entity: 'customer')),
				RecurringMessage::cron('0 17 * * *', new ConnectorsBoostlingoFetchMessage(entity: 'invoices')),
				RecurringMessage::cron('0 18 * * 0-6', new ConnectorsBoostlingoFetchMessage(entity: 'contact')),
				RecurringMessage::cron('0 8 * * 0-6', new ConnectorsBoostlingoFetchMessage(entity: 'customer')),
			)
			->stateful($this->cache);
	}
}
