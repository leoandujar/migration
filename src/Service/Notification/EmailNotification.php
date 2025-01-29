<?php

namespace App\Service\Notification;

class EmailNotification extends Notification
{
	public ?string $from;
	public ?string $template;

	public function __construct(?int $type, ?string $name, ?string $target, ?array $data, ?int $countFailed, ?string $from, ?string $template)
	{
		parent::__construct($type, $name, $target, $data, $countFailed);
		$this->from     = $from;
		$this->template = $template;
	}
}
