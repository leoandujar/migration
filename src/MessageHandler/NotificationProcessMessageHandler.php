<?php

namespace App\MessageHandler;

use App\Apis\Shared\Util\PostmarkService;
use App\Connector\Team\TeamConnector;
use App\Message\NotificationProcessMessage;
use App\Service\LoggerService;
use App\Service\MailerService;
use App\Service\Notification\Notification;
use App\Service\Notification\NotificationService;
use App\Service\Twilio\TwilioService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NotificationProcessMessageHandler
{
	public const NOTIFICATION_TYPE_TEAM = 1;
	public const NOTIFICATION_TYPE_PM_EMAIL = 2;
	public const NOTIFICATION_TYPE_SMS = 3;
	public const NOTIFICATION_TYPE_MAILER_EMAIL = 4;

	private mixed $teamsWebhook;
	private TwilioService $twilioSrv;
	private LoggerService $loggerSrv;
	private TeamConnector $teamConnector;
	private PostmarkService $postmarkService;
	private MailerService $mailerSrv;
	private ParameterBagInterface $parameterBag;
	private bool $qaEnabled;
	private NotificationService $notyService;

	/**
	 * NotificationService constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		PostmarkService $postmarkService,
		MailerService $mailerSrv,
		ParameterBagInterface $parameterBag,
		TwilioService $twilioSrv,
		TeamConnector $teamConnector,
		NotificationService $notyService,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->postmarkService = $postmarkService;
		$this->parameterBag = $parameterBag;
		$this->teamConnector = $teamConnector;
		$this->mailerSrv = $mailerSrv;
		$this->teamsWebhook = $parameterBag->get('teams.default.webhook');
		$this->qaEnabled = $parameterBag->get('app.qa_enabled');
		$this->twilioSrv = $twilioSrv;
		$this->notyService = $notyService;
	}

	/**
	 * @throws \RedisException
	 */
	public function __invoke(NotificationProcessMessage $message): int
	{
		$dataMessage = $message->getData();
		do {
			if (($notify = unserialize($dataMessage)) === false || !$notify instanceof Notification) {
				throw new \Exception("Unable to unserialize payload with data $dataMessage");
			}
			try {
				switch ($notify->type) {
					case self::NOTIFICATION_TYPE_TEAM:
						$this->teamConnector->send($notify->target, (array) $notify->data);
						break;
					case self::NOTIFICATION_TYPE_SMS:
						$this->twilioSrv->Send($notify->target, $notify->smsText);
						break;
					case self::NOTIFICATION_TYPE_PM_EMAIL:
						if (!empty($notify->template)) {
							$this->postmarkService->sendEmailRemoteTemplate(
								$notify->from,
								$notify->target,
								$notify->template,
								(array) $notify->data
							);
						} else {
							$this->postmarkService->sendEmailLocalTemplate($notify->from, $notify->target, 'subject');
						}
						break;
					case self::NOTIFICATION_TYPE_MAILER_EMAIL:
						if (empty($notify->template)) {
							throw new BadRequestHttpException("Unable to find mailer template $notify->template");
						}
						if ($this->qaEnabled) {
							if (is_array($notify->target)) {
								foreach ($notify->target as $index => $target) {
									if (false === stripos('@avantpage.com', $target)) {
										unset($notify->target[$index]);
									}
								}
							} elseif (false === stripos('@avantpage.com', $notify->target)) {
								break;
							}
						}
						$data = [
							'subject' => $notify->subject,
							'from' => $notify->from,
							'fromName' => $notify->fromName,
							'to' => $notify->target,
							'template' => $notify->template,
							'data' => (array) $notify->data,
							'attachments' => (array) $notify->attachments,
						];

						$notify->data = $data;
						if (!$this->mailerSrv->processSendEmail($notify->template, $data)) {
							throw new BadRequestHttpException('Unable to send mailer mail.');
						}
						break;
				}
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error while sending notification', $thr);
				$this->loggerSrv->addWarning(sprintf('notification %s failed %d times adding in the end of the queue', $notify->name, $notify->countFailed));
				$this->notyService->addNotification($notify->type, $notify->target, (array) $notify->data, $notify->name ?? $notify->title ?? null, ++$notify->countFailed);
			}
		} while (0);

		return 0;
	}
}
