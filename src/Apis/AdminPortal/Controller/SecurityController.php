<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\Shared\Http\Request\LoginRequest;
use App\Apis\Shared\Http\Request\Security\RefreshTokenRequest;
use App\Apis\Shared\Util\MercureTokenProvider;
use Symfony\Component\Mercure\Discovery;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\AdminPortal\Handlers\SecurityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\AdminPortal\Http\Request\Security\PublicLoginRequest;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\AzureClient;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;
use App\Apis\AdminPortal\Http\Request\Security\LoginMicrosoftRequest;
use App\Apis\Shared\Listener\PublicEndpoint;

#[Route(path: '')]
class SecurityController extends AbstractController
{
	private LoggerService $loggerSrv;
	private SecurityHandler $securityHandler;

	public function __construct(
		SecurityHandler $securityHandler,
		LoggerService $loggerSrv
	) {
		$this->securityHandler = $securityHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[PublicEndpoint]
	#[Route('/login/{id}', name: 'ap_admin_public_login', methods: ['GET'])]
	public function publicLogin(string $id): ErrorResponse|Response
	{
		try {
			$params = ['id' => $id];
			$requestObj = new PublicLoginRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processPublicLogin($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during public login process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('/login', name: 'ap_login', methods: ['POST'])]
	public function login(Request $request, Discovery $discovery, MercureTokenProvider $tokenProvider): ErrorResponse|Response
	{
		try {
			$requestObj = new LoginRequest($request->getPayload()->all(), $request->headers);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['ip' => $request->getClientIp()]);
			$response = $this->securityHandler->processLogin($params);
			$discovery->addLink($request);
			$response->headers->set(
				'set-cookie',
				"mercureAuthorization={$tokenProvider->getJwt()};SameSite=None; Secure;"
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during login process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('/microsoft/login', name: 'ap_login_sso_microsoft', methods: ['GET'])]
	public function loginOauth2Microsoft(Request $request, ParameterBagInterface $bag, ClientRegistry $clientRegistry): ErrorResponse|Response
	{
		try {
			$url = $clientRegistry->getClient('microsoft')->getOAuth2Provider()->getAuthorizationUrl([
				'scopes' => ['profile', 'email'],
				'redirect_uri' => $bag->get('azure.oauth2.redirect_uri'),
				'login_hint' => $request->query->get('login_hint') ?? '',
				'domain_hint' => $request->query->get('domain_hint') ?? '',
				'state' => hash('sha256', uniqid(), false).'@'.$request->query->get('ref') ?? '',
			]);

			return new Response(json_encode([
				'url' => $url,
			]));
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during microsoft login process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('/microsoft/check', name: 'ap_login_sso_microsoft_check', methods: ['POST'])]
	public function loginOauth2MicrosoftCheck(Request $request, ParameterBagInterface $bag, ClientRegistry $clientRegistry): ApiResponse
	{
		try {
			$r = $request->getContent();
			$r = serialize($r);
			$this->loggerSrv->addError("Error during microsoft login check process $r");
			$requestObj = new LoginMicrosoftRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = $requestObj->getParams();
			/** @var AzureClient $client */
			$client = $clientRegistry->getClient('microsoft');

			$accessToken = $client->getOAuth2Provider()->getAccessToken(
				'authorization_code',
				array_merge(['code' => $params['code']], [
					'redirect_uri' => $bag->get('azure.oauth2.redirect_uri'),
				])
			);

			if (empty($accessToken)) {
				return new ErrorResponse(Response::HTTP_UNAUTHORIZED, 'empty_access_token', 'Unable to get the access token.');
			}

			/** @var AzureResourceOwner $user */
			$user = $client->fetchUserFromToken($accessToken);
			if (!$user) {
				return new ErrorResponse(Response::HTTP_UNAUTHORIZED, 'microsoft_user_not_found', 'Unable to get the user information from Azure.');
			}

			$userInfo = $client->getOAuth2Provider()->get('me', $accessToken, $headers = []);

			$params = array_merge($user->toArray(), $userInfo, $params, ['ip' => $request->getClientIp()]);

			$stateSplitted = explode('@', $params['state']);

			if (count($stateSplitted) > 1) {
				$ref = array_pop($stateSplitted);
				if (!empty($ref)) {
					$params['ref'] = $ref;
				}
			}

			$response = $this->securityHandler->processLogin($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during microsoft login check process.', $thr);
			$response = new ErrorResponse($thr->getCode(), ApiError::CODE_OAUTH2_ERROR, $thr->getMessage());
		}

		return $response;
	}

	#[Route('/cp-login/{id}', name: 'ap_cp_login', methods: ['GET'])]
	public function cpLogin(Request $request, string $id): ErrorResponse|Response
	{
		try {
			$params = ['id' => $id];
			$requestObj = new PublicLoginRequest($params);
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['ip' => $request->getClientIp()]);
			$response = $this->securityHandler->processCpLogin($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during login to cp process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/logout', name: 'ap_logout', methods: ['GET'])]
	public function logout(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processLogout($request);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during logout process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('ping', name: 'ap_ping', methods: ['GET'])]
	public function ping(): ApiResponse|ErrorResponse|Response
	{
		try {
			$response = new ApiResponse(message: 'pong');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during pong process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('/token', name: 'ap_refresh_token', methods: ['POST'])]
	public function refreshToken(Request $request): ApiResponse
	{
		try {
			$requestObj = new RefreshTokenRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processRefreshToken($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in refresh token process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
