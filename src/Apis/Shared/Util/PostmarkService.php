<?php

namespace App\Apis\Shared\Util;

use App\Service\LoggerService;
use Postmark\PostmarkClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostmarkService
{
	public const MAX_RECIPIENT_ADDRESS = 50;
	public const SENDER_NOTIFICATIONS = 1;

	public const EMAIL_TEMPLATE_PUB_LOGIN = 1;
	public const EMAIL_TEMPLATE_WORKFLOW = 2;
	public const EMAIL_TEMPLATE_PROJECT = 3;
	public const EMAIL_TEMPLATE_CREATE = 4;

	private PostmarkClient $client;
	private string $senderNotifications;
	private LoggerService $loggerSrv;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		ParameterBagInterface $parameterBag,
		LoggerService $loggerSrv
	) {
		$this->client = new PostmarkClient($parameterBag->get('app.postmark_api_key'));
		$this->senderNotifications = $parameterBag->get('app.postmark_sender.notifications');
		$this->loggerSrv = $loggerSrv;
		$this->parameterBag = $parameterBag;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	/**
	 * @throws \Throwable
	 */
	public function sendEmailRemoteTemplate(?int $sender, $to, string $templateId, array $templateData): ?bool
	{
		if (is_array($to)) {
			if (count($to) > self::MAX_RECIPIENT_ADDRESS) {
				return null;
			}
			$to = implode(',', $to);
		}

		$response = $this->client->sendEmailWithTemplate(
			$this->getSender($sender),
			$to,
			$templateId,
			$templateData
		);

		if (0 !== $response->__get('errorCode') && 'OK' !== $response->__get('message')) {
			$this->loggerSrv->addError("Error sending email=> {$response->__get('message')}");

			return null;
		}

		return true;
	}

	public function sendEmailLocalTemplate(int $sender, $to, string $subject, string $htmlBody = null, string $textBody = null): ?bool
	{
		if (is_array($to)) {
			if (count($to) > self::MAX_RECIPIENT_ADDRESS) {
				return null;
			}
			$to = implode(',', $to);
		}
		if (!$sender) {
			$sender = $this->senderNotifications;
		}
		$response = $this->client->sendEmail(
			$sender,
			$to,
			$subject,
			$htmlBody,
			$textBody
		);

		if (0 !== $response->__get('errorCode') && 'OK' !== $response->__get('message')) {
			$this->loggerSrv->addError("Error sending email=> {$response->__get('message')}");

			return null;
		}

		return true;
	}

	/**
	 * @throws \Throwable
	 */
	public function getTemplateId(int $template): string
	{
		return match ($template) {
			self::EMAIL_TEMPLATE_PUB_LOGIN => $this->parameterBag->get('app.postmark.tpl_id.pub_login'),
			self::EMAIL_TEMPLATE_WORKFLOW => $this->parameterBag->get('app.postmark.tpl_id.workflow'),
			self::EMAIL_TEMPLATE_PROJECT => $this->parameterBag->get('app.postmark.tpl_id.project'),
			self::EMAIL_TEMPLATE_CREATE => $this->parameterBag->get('app.postmark.tpl_id.create'),
			default => throw new \InvalidArgumentException('The template id is invalid.'),
		};
	}

	/**
	 * @throws \Throwable
	 */
	private function getSender(int $sender): string
	{
		return match ($sender) {
			self::SENDER_NOTIFICATIONS => $this->senderNotifications,
			default => throw new \InvalidArgumentException('The sender id is invalid.'),
		};
	}
}
