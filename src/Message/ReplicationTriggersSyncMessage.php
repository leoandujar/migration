<?php

namespace App\Message;

final class ReplicationTriggersSyncMessage
{
	private string $name;
	private ?string $start;
	private ?int $perPage;

	public function __construct(string $name, string $start, int $perPage)
	{
		$this->name = $name;
		$this->start = $start;
		$this->perPage = $perPage;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getStart(): string
	{
		return $this->start;
	}

	public function getPerPage(): int
	{
		return $this->perPage;
	}
}
