<?php

namespace App\Message;

final class ConnectorsBoostlingoFetchMessage
{
	private ?string $id;
	private ?string $entity;
	private ?string $startDate;
	private ?string $endDate;
	private ?string $since;
	private ?int $onlyDequeue;

	public function __construct(
		?int $onlyDequeue = null,
		?string $id = null,
		?string $entity = null,
		?string $startDate = null,
		?string $endDate = null,
		?string $since = null,
	) {
		$this->id = $id;
		$this->entity = $entity;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->since = $since;
		$this->onlyDequeue = $onlyDequeue;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getEntity(): ?string
	{
		return $this->entity;
	}

	public function getStartDate(): ?string
	{
		return $this->startDate;
	}

	public function getEndDate(): ?string
	{
		return $this->endDate;
	}

	public function getSince(): ?string
	{
		return $this->since;
	}

	public function getOnlyDequeue(): ?int
	{
		return $this->onlyDequeue;
	}
}
