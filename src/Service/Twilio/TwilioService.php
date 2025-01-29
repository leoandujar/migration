<?php

namespace App\Service\Twilio;

use Twilio\Rest\Client;
use App\Service\LoggerService;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TwilioService
{
	private Client $client;
	private mixed $number;

	public function __construct(ParameterBagInterface $bag, LoggerService $loggerSrv)
	{
		try {
			$this->client = new Client($bag->get('twilio_id'), $bag->get('twilio_token'), $bag->get('twilio_aid'));
			$this->number = $bag->get('twilio_number');
		} catch (\Throwable $thr) {
			$loggerSrv->addError($thr);
		}
	}

	/**
	 * @throws TwilioException
	 */
	public function send(string $phone, string $text): MessageInstance
	{
		return $this->client->messages->create($phone, [
			'from' => $this->number,
			'body' => $text,
		]);
	}
}
