<?php

namespace App\Message;

final class WorkflowCleanHistoryMessage
{
	private string $days;

	public function __construct(string $days)
	{
		$this->days = $days;
	}

	public function getDays(): string
	{
		return $this->days;
	}
}
