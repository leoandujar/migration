<?php

namespace App\Linker\Controller;

use App\Apis\Shared\Listener\PublicEndpoint;
use App\Message\ConnectorsPostmarkProcessMessage;
use Postmark\Inbound;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[Route(path: '/webhooks/postmark')]
class PostmarkController extends AbstractController
{
	private LoggerService $loggerSrv;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WEBHOOKS);
		$this->bus = $bus;
	}

	#[PublicEndpoint]
	#[Route(path: '/', name: 'postmark_webhook', methods: ['GET', 'POST'])]
	public function postmarkWebhook(): Response
	{
		try {
			$payload = file_get_contents('php://input');
			if (empty($payload)) {
				$this->loggerSrv->addError('Postmark Webhook has empty payload');

				return new Response(null, Response::HTTP_BAD_REQUEST);
			}

			$inbound = new Inbound($payload);
			$decodedData = json_decode($payload, true);
			if (!$decodedData) {
				$this->loggerSrv->addError('Postmark WEBHOOK could not decode payload');

				return new Response(null, Response::HTTP_OK);
			}
			$tag = $inbound->Tag();
			if (!$tag) {
				throw new BadRequestException('Postmark webhook arrived with empty tag. Not allowed.');
			}

			$tagData = json_decode($tag, true);

			if (!$tagData) {
				throw new BadRequestException('Postmark webhook arrived with non json value in field Tag. Not allowed.');
			}
			$workflowType = $tagData['workflowType'] ?? null;
			$workflowName = $tagData['workflowName'] ?? 'EPProjectTest';
			$type = $tagData['type'];
			$data = (object) [
				'countFailed' => 0,
				'type' => $type,
				'workflowName' => $workflowName,
				'workflowType' => $workflowType,
				'smsText' => $tagData['message'],
				'messageFrom' => $inbound->FromName(),
				'messageId' => $inbound->Headers('Message-ID'),
				'data' => $decodedData,
			];
			$this->loggerSrv->addInfo("Postmark Webhook: workflow $workflowName to the queue.");
			// $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_POSTMARK_WEBHOOK_QUEUE, serialize($data));
			try {
				$this->bus->dispatch(new ConnectorsPostmarkProcessMessage(data: $data));
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError("Postmark webhook arrived with error: {$thr}");
			}

		} catch (\Throwable $thr) {
			$this->loggerSrv->addInfo('Error while processing webhook for Postmark', $thr);

			return new Response(null, Response::HTTP_BAD_REQUEST);
		}

		return new Response();
	}
}
