<?php

namespace App\Message;

final class XtmLqaProcessMessage
{
	private int $start;
	private int $limit;
	private string $one;

	public function __construct(
		?int $start = 0,
		?int $limit = 100,
		?string $one = '',
	) {
		$this->start = $start;
		$this->limit = $limit;
		$this->one = $one;
	}

	public function getStart(): int
	{
		return $this->start;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}

	public function getOne(): string
	{
		return $this->one;
	}
}
