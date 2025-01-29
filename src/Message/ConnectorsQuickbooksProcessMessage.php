<?php

namespace App\Message;

final class ConnectorsQuickbooksProcessMessage
{
	private ?object $data;

	public function __construct(
		?object $data = null,
	) {
		$this->data = $data;
	}

	public function getData(): ?object
	{
		return $this->data;
	}
}
