<?php

namespace App\Message;

final class XtmProjectsLinkMessage
{
	private int $limit;

	public function __construct(int $limit)
	{
		$this->limit = $limit;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}
}
