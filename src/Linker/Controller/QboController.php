<?php

namespace App\Linker\Controller;

use App\Apis\Shared\Listener\PublicEndpoint;
use App\Linker\Services\QboService;
use App\Service\LoggerService;
use App\Connector\Qbo\QboConnector;
use App\Linker\Services\RedisClients;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/qbo')]
class QboController extends AbstractController
{
	private mixed $accessToken;
	private LoggerService $loggerSrv;
	private QboConnector $qboConnector;
	private QboService $qboWebhookSrv;

	public function __construct(
		LoggerService $loggerSrv,
		QboConnector $qboConnector,
		RedisClients $redisClients,
		QboService $qboWebhookSrv,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->qboConnector = $qboConnector;
		$this->qboWebhookSrv = $qboWebhookSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
		$this->accessToken = unserialize(base64_decode($redisClients->redisMainDB->get(RedisClients::SESSION_KEY_QBO_TOKEN)));
	}

	#[PublicEndpoint]
	#[Route(path: '/token', name: 'qbo_oauth_main', methods: ['GET', 'POST'])]
	public function mainCallback(): Response
	{
		$result = $this->qboConnector->getAuthUrl();
		$isTokenExpired = false;
		$existsToken = true;

		do {
			if (!$this->accessToken) {
				$existsToken = false;
				break;
			}
			$isTokenExpired = $this->qboConnector->isTokenExpired();
		} while (0);

		return $this->render('QuickBooks/index.html.twig', [
			'authUrl' => $result,
			'existsToken' => $existsToken,
			'isTokenExpired' => $isTokenExpired,
		]);
	}

	#[PublicEndpoint]
	#[Route(path: '/oauth/redirect', name: 'qbo_oauth_redirect', methods: ['GET', 'POST'])]
	public function oauthRedirectCallback(Request $request): JsonResponse
	{
		$this->loggerSrv->addInfo('Received QuickBooks Callback request for token exchange');

		$code = $request->query->get('code');
		$realmId = $request->query->get('realmId');

		if (empty($code) || empty($realmId)) {
			return new JsonResponse(['result' => 'Failed'], Response::HTTP_BAD_REQUEST);
		}

		$this->qboConnector->getToken($code, $realmId);

		return new JsonResponse(['result' => 'OK']);
	}

	#[PublicEndpoint]
	#[Route(path: '/oauth/refreshToken', name: 'qbo_oauth_refresh', methods: ['GET', 'POST'])]
	public function refreshToken(): RedirectResponse
	{
		do {
			if (!$this->accessToken) {
				break;
			}
			if ($this->qboConnector->isTokenExpired()) {
				$this->qboConnector->refreshToken();
			}
		} while (0);

		return $this->redirectToRoute('qbo_oauth_main');
	}

	#[PublicEndpoint]
	#[Route(path: '/webhook', name: 'qbo_webhook', methods: ['GET', 'POST'])]
	public function qboWebhook(Request $request): Response
	{
		$payload = file_get_contents('php://input');
		$this->qboWebhookSrv->processWebhook($payload, $request->headers->get('intuit-signature'));

		return new Response();
	}
}
