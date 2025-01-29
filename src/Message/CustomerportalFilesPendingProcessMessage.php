<?php

namespace App\Message;

final class CustomerportalFilesPendingProcessMessage
{
	private mixed $data;

	public function __construct(mixed $data)
	{
		$this->data = $data;
	}

	public function getData(): mixed
	{
		return $this->data;
	}
}
