<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Apis\AdminPortal\Http\Request\Security\LoginFromAdminPortalRequest;
use App\Apis\CustomerPortal\Http\Request\Security\PublicLoginCreateRequest;
use App\Apis\CustomerPortal\Http\Request\Security\PublicLoginRequest;
use App\Apis\CustomerPortal\Http\Request\Security\TokenRequest;
use App\Apis\Shared\Http\Request\LoginRequest;
use App\Apis\Shared\Http\Request\Security\RefreshTokenRequest;
use App\Apis\Shared\Listener\PublicEndpoint;
use App\Apis\Shared\Util\MercureTokenProvider;
use Symfony\Component\Mercure\Discovery;
use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\SecurityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Apis\CustomerPortal\Http\Request\Security\RecoveryPassConfirmRequest;
use App\Apis\CustomerPortal\Http\Request\Security\RecoveryPasswordRequestInit;

#[Route('/')]
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
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[PublicEndpoint]
	#[Route('login', name: 'cp_login', methods: ['POST'])]
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
				"mercureAuthorization={$tokenProvider->getJwt()}; domain=.avantpage.app; path=/; httponly; samesite=lax;"
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during login process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('public/login', name: 'cp_public_login', methods: ['POST'])]
	public function publicLogin(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new PublicLoginRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processPublicLogin($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during public login process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('public/signup', name: 'cp_public_signup', methods: ['POST'])]
	public function createUserPublicLogin(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new PublicLoginCreateRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processCreateUserPublicLogin($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during public login create process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('public/authenticate', name: 'cp_public_authenticate', methods: ['POST'])]
	public function authenticatePublicLogin(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new TokenRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['ip' => $request->getClientIp()]);
			$response = $this->securityHandler->processAuthenticatePublicLogin($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during authenticate public login process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('public/ap', name: 'cp_public_ap', methods: ['POST'])]
	public function loginFromAdminPortal(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new LoginFromAdminPortalRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$params = array_merge($requestObj->getParams(), ['ip' => $request->getClientIp()]);
			$response = $this->securityHandler->processLoginFromAdminPortal($params);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during authenticate from admin portal.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('2fa', name: 'cp_2fa', methods: ['POST'])]
	public function tfa(Request $request, Discovery $discovery, MercureTokenProvider $tokenProvider): ErrorResponse|Response
	{
		try {
			$response = $this->securityHandler->processLogin(
				params: [
					'user' => $request->attributes->get('user'),
					'ip' => $request->getClientIp(),
				],
				verifyPass: false
			);
			$discovery->addLink($request);
			$response->headers->set(
				'set-cookie',
				"mercureAuthorization={$tokenProvider->getJwt()}; domain=.avantpage.app; path=/; httponly; samesite=lax;"
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during public 2fa process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('logout', name: 'cp_logout', methods: ['GET'])]
	public function logout(): ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processLogout();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during logout process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('token', name: 'cp_refresh_token', methods: ['POST'])]
	public function refreshToken(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new RefreshTokenRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processRefreshToken($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during refresh token process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('ping', name: 'cp_ping', methods: ['GET'])]
	public function ping(): ApiResponse|ErrorResponse|Response
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = new ApiResponse(message: 'pong');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during pong process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('password-recovery-init', name: 'cp_password_recovery_init', methods: ['POST'])]
	public function passwordRecoveryInit(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new RecoveryPasswordRequestInit($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processRecoveryPasswordInit($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error trying to recovery password in first step.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route('password-recovery-confirm', name: 'cp_password_recovery_confirm', methods: ['POST'])]
	public function passwordRecoveryConfirm(Request $request): ErrorResponse|Response
	{
		try {
			$requestObj = new RecoveryPassConfirmRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->securityHandler->processPasswordRecoveryConfirm($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error during password recovery confirm process.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
