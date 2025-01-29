<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class ConnectorsHubspotProcessMessage
{
	private mixed $data;

	public function __construct(mixed $data)
	{
		$this->data = $data;
	}

	public function getData(): mixed
	{
		return $this->data;
	}
}
