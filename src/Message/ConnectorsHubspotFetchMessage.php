<?php

namespace App\Message;

final class ConnectorsHubspotFetchMessage
{
	private ?string $id;
	private ?string $entity;
	private ?string $onlyDequeue;
	private ?string $updateRemote;

	public function __construct(
		?string $id = null,
		?string $entity = null,
		?string $onlyDequeue = null,
		?string $updateRemote  = null,
	) {
		$this->id = $id;
		$this->entity = $entity;
		$this->onlyDequeue = $onlyDequeue;
		$this->updateRemote = $updateRemote;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getEntity(): ?string
	{
		return $this->entity;
	}

	public function getOnlyDequeue(): ?string
	{
		return $this->onlyDequeue;
	}

	public function getUpdateRemote(): ?string
	{
		return $this->updateRemote;
	}
}
