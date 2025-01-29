<?php

namespace App\Service\Notification;

class SmsNotification extends Notification
{
	public mixed $smsText;

	public function __construct(?int $type, ?string $name, ?string $target, ?array $data, ?int $countFailed)
	{
		parent::__construct($type, $name, $target, $data, $countFailed);
		$this->smsText = $data['smsText'] ?? '';
	}
}
