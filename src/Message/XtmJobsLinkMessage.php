<?php

namespace App\Message;

final class XtmJobsLinkMessage
{
	private int $limit;

	public function __construct(?int $limit)
	{
		$this->limit = $limit ?? 100;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}
}
