<?php

namespace App\Connector\Team;

use GuzzleHttp\Client;
use App\Service\LoggerService;

class TeamConnector
{
	private Client $client;
	private LoggerService $loggerSrv;

	/**
	 * TeamConnector constructor.
	 */
	public function __construct(LoggerService $loggerSrv)
	{
		$this->client    = new Client();
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	/**
	 * @throws \Throwable
	 */
	public function send($webhook, $data): void
	{
		try {
			$card = new Card($data);
			if (!$webhook) {
				throw new \Exception('team webhook url is required');
			}
			$this->client->post($webhook, [
				'json' => $card->getMessage(),
			]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('team notification error: %s', $thr->getMessage()), $thr);
			throw $thr;
		}
	}
}
