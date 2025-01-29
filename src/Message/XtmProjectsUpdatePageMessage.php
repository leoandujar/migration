<?php

namespace App\Message;

final class XtmProjectsUpdatePageMessage
{
	private string $page;
	private string $date;

	public function __construct(string $page, string $date)
	{
		$this->page = $page;
		$this->date = $date;
	}

	public function getPage(): string
	{
		return $this->page;
	}

	public function getDate(): string
	{
		return $this->date;
	}
}
