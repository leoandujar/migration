<?php

namespace App\Service\Notification;

use App\Message\NotificationProcessMessage;
use App\Service\MailerService;
use App\Service\LoggerService;
use App\Service\Twilio\TwilioService;
use App\Connector\Team\TeamConnector;
use App\Linker\Services\RedisClients;
use App\Apis\Shared\Util\PostmarkService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationService
{
	public const NOTIFICATION_TYPE_TEAM = 1;
	public const NOTIFICATION_TYPE_PM_EMAIL = 2;
	public const NOTIFICATION_TYPE_SMS = 3;
	public const NOTIFICATION_TYPE_MAILER_EMAIL = 4;
	public const NOTIFICATION_TOTAL_PER_ROUND = 50;

	public const COUNT_FAILURE_NOTIFICATIONS = 60;

	private mixed $teamsWebhook;
	private TwilioService $twilioSrv;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private TeamConnector $teamConnector;
	private PostmarkService $postmarkService;
	private MailerService $mailerSrv;
	private ParameterBagInterface $parameterBag;
	private bool $qaEnabled;
	private MessageBusInterface $bus;

	/**
	 * NotificationService constructor.
	 */
	public function __construct(
		RedisClients $redisClients,
		LoggerService $loggerSrv,
		PostmarkService $postmarkService,
		MailerService $mailerSrv,
		ParameterBagInterface $parameterBag,
		TwilioService $twilioSrv,
		TeamConnector $teamConnector,
		MessageBusInterface $bus,
	) {
		$this->redisClients = $redisClients;
		$this->loggerSrv = $loggerSrv;
		$this->postmarkService = $postmarkService;
		$this->parameterBag = $parameterBag;
		$this->teamConnector = $teamConnector;
		$this->mailerSrv = $mailerSrv;
		$this->teamsWebhook = $parameterBag->get('teams.default.webhook');
		$this->qaEnabled = $parameterBag->get('app.qa_enabled');
		$this->twilioSrv = $twilioSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->bus = $bus;
	}

	/**
	 * @param int $countFailed
	 */
	public function addNotification(string $type, mixed $target, array $data, ?string $name = null, $countFailed = 0): void
	{
		$notify = null;
		switch ($type) {
			case NotificationService::NOTIFICATION_TYPE_TEAM:
				if (null === $target) {
					$target = $this->teamsWebhook;
				}
				$notify = new TeamNotification($type, $name, $target, $data, $countFailed);
				break;
			case self::NOTIFICATION_TYPE_PM_EMAIL:
				$template = $data['template'] ?? $this->parameterBag->get('app.postmark.tpl_id.workflow');
				unset($data['template']);
				$notify = new EmailNotification($type, $name, $target, $data, $countFailed, PostmarkService::SENDER_NOTIFICATIONS, $template);
				break;
			case self::NOTIFICATION_TYPE_MAILER_EMAIL:
				$subject = $data['subject'] ?? null;
				$from = $data['from'] ?? $this->mailerSrv->senderNotificationAddress;
				$fromName = $data['fromName'] ?? $this->mailerSrv->customerPortalTitle;
				$template = $data['template'] ?? MailerService::DEFAULT_TEMPLATE;
				$attachments = $data['attachments'] ?? [];
				$data = $data['data'] ?? [];
				$notify = new MailerNotification($type, $name, $target, $data, $countFailed, $from, $template, $subject, $fromName, $attachments);
				break;
			case self::NOTIFICATION_TYPE_SMS:
				$notify = new SmsNotification($type, $name, $target, $data, $countFailed);
				break;
			default:
				$this->loggerSrv->addInfo(sprintf('no notification configure for %s', $name));
				break;
		}
		if (null !== $notify) {
			if ($countFailed > self::COUNT_FAILURE_NOTIFICATIONS) {
				$this->loggerSrv->addError("Notification $notify->name exceeded the maximum of allowed  attempts. It will not be added to the queue", json_encode($notify));
			} else {
				try {
					$this->bus->dispatch(new NotificationProcessMessage(serialize($notify)));
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error while sending notification', $thr);
				}
			}
		}
	}
}
