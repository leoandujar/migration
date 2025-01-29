<?php

namespace App\Service\Notification;

class TeamNotification extends Notification
{
	public const STATUS_SUCCESS = 'success';
	public const STATUS_FAILURE = 'failure';

	public mixed $message;
	public mixed $status;
	public mixed $title;

	public function __construct(?int $type, ?string $name, mixed $target, ?array $data, ?int $countFailed)
	{
		parent::__construct($type, $name, $target, $data, $countFailed);
		$this->message = $data['message'] ?? '';
		$this->status  = $data['status'] ?? '';
		$this->title   = $data['title'] ?? '';
	}
}
