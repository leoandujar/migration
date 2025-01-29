<?php

namespace App\Message;

final class ConnectorsQuickbooksFetchMessage
{
	private string $id;
	private string $entity;

	public function __construct(string $id, string $entity)
	{
		$this->id = $id;
		$this->entity = $entity;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getEntity(): string
	{
		return $this->entity;
	}
}
