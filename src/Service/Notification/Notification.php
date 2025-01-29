<?php

namespace App\Service\Notification;

abstract class Notification
{
	public ?int $type;
	public ?string $name;
	public mixed $target;
	public ?array $data;
	public ?int $countFailed;

	public function __construct(?int $type, ?string $name, mixed $target, ?array $data, ?int $countFailed)
	{
		$this->type = $type;
		$this->name = $name;
		$this->target = $target;
		$this->data = $data;
		$this->countFailed = $countFailed;
	}

	public function encode(): string
	{
		return json_encode($this);
	}
}
