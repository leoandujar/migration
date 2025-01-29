<?php

namespace App\Message;

final class ConnectorsPostmarkProcessMessage
{
	private ?int $dequeueLimit;
	private ?object $data;

	public function __construct(?int $dequeueLimit = null, ?object $data = null)
	{
		$this->dequeueLimit = $dequeueLimit;
		$this->data = $data;
	}

	public function getDequeueLimit(): ?int
	{
		return $this->dequeueLimit;
	}

	public function getData(): ?object
	{
		return $this->data;
	}
}
