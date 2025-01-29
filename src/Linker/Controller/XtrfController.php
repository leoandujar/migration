<?php

namespace App\Linker\Controller;

use App\Apis\Shared\Listener\PublicEndpoint;
use App\Linker\Handler\XtrfHandler;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/webhooks/xtrf')]
class XtrfController extends AbstractController
{
	private LoggerService $loggerSrv;
	private XtrfHandler $xtrfHandler;

	public function __construct(
		LoggerService $loggerSrv,
		XtrfHandler $xtrfHandler,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WEBHOOKS);
		$this->xtrfHandler = $xtrfHandler;
	}

	#[PublicEndpoint]
	#[Route(path: '/{event}', name: 'xtrf_webhook', methods: ['GET', 'POST'])]
	public function xtrfWebhook(?string $event = null): Response
	{
		try {
			$payload = file_get_contents('php://input');
			if (empty($payload)) {
				$this->loggerSrv->addError("XTRF Webhook $event has empty payload");

				return new Response();
			}

			$payload = json_decode($payload, true);

			return $this->xtrfHandler->processXtrfWebhook($event, $payload);

		} catch (\Throwable $thr) {
			$this->loggerSrv->addInfo('Error while processing webhook from XTRF', $thr);
		}

		return new Response();
	}
}
