<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
	public const DEFAULT_TEMPLATE = 'notification';

	private MailerInterface $mailer;
	private LoggerService $loggerSrv;
	public string $adminPortalTitle;
	public string $customerPortalTitle;
	public string $senderNoReplyAddress;
	public string $senderSupportAddress;
	public string $senderNotificationAddress;

	public function __construct(
		MailerInterface $mailer,
		ParameterBagInterface $parameterBag,
		LoggerService $loggerSrv
	) {
		$this->mailer = $mailer;
		$this->loggerSrv = $loggerSrv;
		$this->adminPortalTitle = $parameterBag->get('admin_portal_title');
		$this->customerPortalTitle = $parameterBag->get('customer_portal_title');
		$this->senderNoReplyAddress = $parameterBag->get('mailer_sender_no_reply');
		$this->senderSupportAddress = $parameterBag->get('mailer_sender_support');
		$this->senderNotificationAddress = $parameterBag->get('mailer_sender_notification');
	}

	public function processSendEmail(string $template, array $context): bool
	{
		$subject = $context['subject'] ?? 'Notification';

		$from = $context['from'] ?? $this->senderNoReplyAddress;
		$fromName = $context['fromName'] ?? $this->customerPortalTitle;
		$to = $context['to'] ?? null;
		$data = $context['data'] ?? [];
		$attachments = $context['attachments'] ?? [];

		if (!$to) {
			$this->loggerSrv->addError('sendNotification missing "TO" value.');

			return false;
		}

		return $this->sendEmail(
			"Emails/$template.html.twig",
			[
				'from' => $from,
				'fromName' => $fromName,
				'to' => $to,
				'subject' => $subject,
				'attachments' => $attachments,
			],
			$data
		);
	}

	private function sendEmail($templateHtml, $options = [], $context = []): bool
	{
		$email = new TemplatedEmail();

		try {
			$email
				->subject($options['subject'])
				->from(new Address($options['from'], $options['fromName']))
				->htmlTemplate($templateHtml)
				->context($context);

			if (is_array($options['to'])) {
				$email->to(...$options['to']);
				$toString = implode(',', $options['to']);
			} else {
				$email->to($options['to']);
				$toString = $options['to'];
			}

			if (key_exists('bcc', $options)) {
				$email->bcc($options['bcc']);
			}

			if (!empty($options['cc'])) {
				$email->cc($options['cc']);
			}

			if (!empty($options['attachments'])) {
				foreach ($options['attachments'] as $attachment) {
					$email->attachFromPath($attachment);
				}
			}

			$this->mailer->send($email);
			if (!empty($options['attachments'])) {
				foreach ($options['attachments'] as $attachment) {
					unlink($attachment);
				}
			}

			$this->loggerSrv->addInfo(sprintf('Email sent success to %s, type: %s, template: %s', $toString, $options['subject'], $templateHtml));

			return true;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('Error sending email to %s, type: %s, template: %s', $toString, $options['subject'], $templateHtml), $context);
			$this->loggerSrv->addError('Error sending email', $thr);

			return false;
		}
	}
}
