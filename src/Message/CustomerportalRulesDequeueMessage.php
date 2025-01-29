<?php

namespace App\Message;

final class CustomerportalRulesDequeueMessage
{
	private ?string $dequeueLimit;

	public function __construct(string $dequeueLimit)
	{
		$this->dequeueLimit = $dequeueLimit;
	}

	public function getDequeueLimit(): ?string
	{
		return $this->dequeueLimit;
	}
}
