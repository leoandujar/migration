<?php

namespace App\Message;

final class CustomerportalFilesProjectsDequeueMessage
{
	private int $limit;
	private ?string $queueName;

	public function __construct(int $limit, ?string $queueName)
	{
		$this->limit = $limit;
		$this->queueName = $queueName;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}

	public function getQueueName(): ?string
	{
		return $this->queueName;
	}
}
