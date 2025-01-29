<?php

namespace App\Message;

final class AdminportalUsersCleanMessage
{
	private string $sinceDate;

	public function __construct(
		string $sinceDate,
	) {
		$this->sinceDate = $sinceDate;
	}

	public function getSinceDate(): string
	{
		return $this->sinceDate;
	}
}
