<?php

namespace App\Message;

final class CustomerportalFilesProjectsProcessMessage
{
	private string $data;
	private ?string $queue;

	public function __construct(string $data, ?string $queue = null)
	{
		$this->data = $data;
		$this->queue = $queue;
	}

	public function getData(): string
	{
		return $this->data;
	}

	public function getQueue(): ?string
	{
		return $this->queue;
	}
}
