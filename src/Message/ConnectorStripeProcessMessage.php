<?php

namespace App\Message;

use App\MessageHandler\ConnectorStripeProcessMessageHandler;

final class ConnectorStripeProcessMessage
{
	private int $limit;

	public function __construct(int $limit = ConnectorStripeProcessMessageHandler::LIMIT_TO_PROCESS)
	{
		$this->limit = $limit;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}
}
