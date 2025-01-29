<?php

namespace App\Message;

final class XtmProjectExtendedMessage
{
	private string $limit;

	public function __construct(string $limit)
	{
		$this->limit = $limit;
	}

	public function getLimit(): string
	{
		return $this->limit;
	}
}
