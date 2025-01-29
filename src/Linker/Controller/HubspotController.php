<?php

namespace App\Linker\Controller;

use App\Apis\Shared\Listener\PublicEndpoint;
use App\Linker\Handler\HubspotHandler;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route(path: '/webhooks/hubspot')]
class HubspotController extends AbstractController
{
	private LoggerService $loggerSrv;
	private ParameterBagInterface $parameterBag;
	private HubspotHandler $hubspotHandler;

	public function __construct(
		LoggerService $loggerSrv,
		ParameterBagInterface $parameterBag,
		HubspotHandler $hubspotHandler,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->parameterBag = $parameterBag;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WEBHOOKS);
		$this->hubspotHandler = $hubspotHandler;
	}

	#[PublicEndpoint]
	#[Route(path: '/', name: 'hubspot_webhook', methods: ['GET', 'POST'])]
	public function hubspotWebhook(Request $request): Response
	{

		try {
			$payload = file_get_contents('php://input');
			if (empty($payload) || false === $payload) {
				return new Response(null, Response::HTTP_BAD_REQUEST);
			}

			$clientSecret = $this->parameterBag->get('hubspot.client_secret');
			$receivedSignatureVersion = $request->headers->get('x-hubspot-signature-version');
			$receivedSignature = $request->headers->get('x-hubspot-signature');
			if (empty($clientSecret) || empty($receivedSignatureVersion) || empty($receivedSignature)) {
				return new Response(null, Response::HTTP_BAD_REQUEST);
			}

			if ('v1' === $receivedSignatureVersion) {
				$sourceString = "$clientSecret$payload";
				$signed = hash('sha256', $sourceString);
			} else {
				return new Response(null, Response::HTTP_BAD_REQUEST);
			}

			if ($signed !== $receivedSignature) {
				$this->loggerSrv->addError("Hubspot sent unknown signature version=> $receivedSignatureVersion");

				return new Response(null, Response::HTTP_UNAUTHORIZED);
			}

			$payload = json_decode($payload, true);

			return $this->hubspotHandler->processHubspotWebhook($payload);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addInfo('Error while processing webhook for HUBSPOT', $thr);

			return new Response(null, Response::HTTP_BAD_REQUEST);
		}
	}
}
