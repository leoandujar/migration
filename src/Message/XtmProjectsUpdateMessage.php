<?php

namespace App\Message;

final class XtmProjectsUpdateMessage
{
	private string $finishedDate;

	public function __construct(string $finishedDate)
	{
		$this->finishedDate = $finishedDate;
	}

	public function getFinishedDate(): string
	{
		return $this->finishedDate;
	}
}
