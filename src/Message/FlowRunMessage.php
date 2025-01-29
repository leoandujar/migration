<?php

namespace App\Message;

final class FlowRunMessage
{
	private string $flowId;
	private ?string $monitorId;

	public function __construct(
		string $flowId,
		?string $monitorId,
	) {
		$this->flowId = $flowId;
		$this->monitorId = $monitorId;
	}

	public function getFlowId(): string
	{
		return $this->flowId;
	}

	public function getMonitorId(): ?string
	{
		return $this->monitorId;
	}
}
