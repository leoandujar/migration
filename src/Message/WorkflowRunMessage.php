<?php

namespace App\Message;

final class WorkflowRunMessage
{
	private string $name;
	private ?int $monitorId;

	public function __construct(string $name, ?int $monitorId)
	{
		$this->name = $name;
		$this->monitorId = $monitorId;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getMonitorId(): ?int
	{
		return $this->monitorId;
	}
}
