<?php

namespace App\Service\Notification;

class MailerNotification extends Notification
{
	public ?string $from;
	public ?string $subject;
	public ?string $fromName;
	public ?string $template;
	public ?array $attachments;

	public function __construct(?int $type, ?string $name, mixed $target, ?array $data, ?int $countFailed, ?string $from, ?string $template, ?string $subject, ?string $fromName, ?array $attachments)
	{
		parent::__construct($type, $name, $target, $data, $countFailed);
		$this->from = $from;
		$this->subject = $subject;
		$this->fromName = $fromName;
		$this->template = $template;
		$this->attachments = $attachments;
	}
}
