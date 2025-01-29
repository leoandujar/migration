<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\CustomerPortal\Security\RedisUserTrait;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Apis\Shared\Util\UtilsService;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Connector\Xtrf\XtrfConnector;
use App\Connector\XtrfMacro\MacroConnector;
use App\Linker\Services\RedisClients;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\CPToken;
use App\Model\Entity\Permission;
use App\Model\Entity\Role;
use App\Model\Repository\ContactPersonRepository;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\PermissionsRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\JwtService;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use App\Service\Twilio\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use ShortCode\Random;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Apis\Shared\Handlers\SecurityHandler as BaseSecurityHandler;

class SecurityHandler
{
	use UserResolver;
	use RedisUserTrait;

	private SessionInterface $session;
	private EntityManagerInterface $em;
	private FileSystemService $fileSystemSrv;
	private JWTTokenManagerInterface $manager;
	private ParameterBagInterface $parameterBag;
	private TokenStorageInterface $tokenStorage;
	private CloudFileSystemService $fileBucketService;
	private PermissionsRepository $permissionsRepository;
	private ContactPersonRepository $contactPersonRepository;
	private CustomerPortalConnector $customerPortalConnector;
	private UserPasswordHasherInterface $encoder;
	private CustomerPortalConnector $clientPortalConnector;
	private XtrfConnector $xtrfConnector;
	private RequestStack $requestStack;
	private UtilsService $utilsSrv;
	private MacroConnector $macroConn;
	private InternalUserRepository $internalUserRepo;
	private RedisClients $redisClients;
	private LoggerService $loggerSrv;
	private TwilioService $twilioSrv;
	private JwtService $jwtSrv;
	private AccountHandler $accountHandler;
	private BaseSecurityHandler $baseSecurityHandler;
	private NotificationService $notificationSrv;

	public function __construct(
		JWTTokenManagerInterface $manager,
		TokenStorageInterface $tokenStorage,
		ContactPersonRepository $contactPersonRepository,
		RedisClients $redisClients,
		LoggerService $loggerSrv,
		TwilioService $twilioSrv,
		PermissionsRepository $permissionsRepository,
		CloudFileSystemService $fileBucketService,
		ParameterBagInterface $parameterBag,
		UtilsService $utilsSrv,
		JwtService $jwtSrv,
		InternalUserRepository $internalUserRepo,
		CustomerPortalConnector $customerPortalConnector,
		FileSystemService $fileSystemSrv,
		RequestStack $requestStack,
		MacroConnector $macroConn,
		EntityManagerInterface $em,
		UserPasswordHasherInterface $encoderInt,
		CustomerPortalConnector $connector,
		XtrfConnector $xtrfConnector,
		AccountHandler $accountHandler,
		BaseSecurityHandler $baseSecurityHandler,
		NotificationService $notificationSrv
	) {
		$this->manager = $manager;
		$this->tokenStorage = $tokenStorage;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->fileBucketService = $fileBucketService;
		$this->session = $requestStack->getSession();
		$this->requestStack = $requestStack;
		$this->parameterBag = $parameterBag;
		$this->customerPortalConnector = $customerPortalConnector;
		$this->em = $em;
		$this->permissionsRepository = $permissionsRepository;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->encoder = $encoderInt;
		$this->clientPortalConnector = $connector;
		$this->xtrfConnector = $xtrfConnector;
		$this->utilsSrv = $utilsSrv;
		$this->macroConn = $macroConn;
		$this->internalUserRepo = $internalUserRepo;
		$this->redisClients = $redisClients;

		$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_CP);
		$this->loggerSrv = $loggerSrv;
		$this->twilioSrv = $twilioSrv;
		$this->jwtSrv = $jwtSrv;
		$this->accountHandler = $accountHandler;
		$this->baseSecurityHandler = $baseSecurityHandler;
		$this->notificationSrv = $notificationSrv;
	}

	public function processPublicLogin(array $dataRequest): ApiResponse
	{
		$email = $dataRequest['email'];
		/** @var ContactPerson $user */
		$user = $this->contactPersonRepository->findOneBy(['email' => $email]);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$customer = $user->getCustomersPerson()?->getCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}
		$customerAndParentRoles = Factory::customerRoles($customer);
		$customerRoles = $customer->getRoles() ?? [];
		$parentRoles = $customer->getParentCustomer()?->getRoles() ?? [];

		if (!in_array(Role::ROLE_CP_PUBLIC_LOGIN, $customerAndParentRoles)) {
			return new ErrorResponse(
				Response::HTTP_FORBIDDEN,
				ApiError::CODE_FORBIDDEN_ACTION,
				ApiError::$descriptions[ApiError::CODE_FORBIDDEN_ACTION]
			);
		}

		if (
			(!in_array(Role::ROLE_CP_PUBLIC_LOGIN, $customerRoles) && in_array(Role::ROLE_CP_PUBLIC_LOGIN, $parentRoles))
			|| !$user->getSystemAccount()
			|| ($user->getSystemAccount() && !$user->getSystemAccount()->getWebLoginAllowed())
		) {
			$createResponse = $this->macroConn->runMacro(
				$this->parameterBag->get('cp.macro_id_create_user'),
				[$dataRequest['customer_id']],
				['cpId' => $user->getId()]
			);
			if (!$createResponse->isSuccessfull()) {
				return new ErrorResponse(
					Response::HTTP_BAD_GATEWAY,
					ApiError::CODE_MACRO_RUN_ERROR,
					ApiError::$descriptions[ApiError::CODE_MACRO_RUN_ERROR]
				);
			}

			$macroStatus = $this->macroConn->checkStatusTilCompleted($createResponse->actionId);

			if (MacroConnector::STATUS_PENDING === $macroStatus) {
				return new ErrorResponse(
					Response::HTTP_BAD_GATEWAY,
					ApiError::CODE_MACRO_RUN_STILL_PENDING,
					ApiError::$descriptions[ApiError::CODE_MACRO_RUN_STILL_PENDING]
				);
			}
		}

		$codeToken = strtoupper(Random::get(6));
		$user->setPublicAuthenticationToken($codeToken);
		$this->em->persist($user);
		$this->em->flush();
		$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $user->getEmail(), [
			'subject' => 'AvantPortal One-Time Password',
			'template' => 'ap-otp',
			'data' => 			[
				'name' => $user->getName(),
				'code' => $codeToken,
			],
		]);

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processCreateUserPublicLogin(array $dataRequest): ApiResponse
	{
		$this->utilsSrv->arrayKeysToCamel($dataRequest);
		$firstName = $dataRequest['firstName'];
		$lastName = $dataRequest['lastName'];
		$email = $dataRequest['email'];

		/** @var ContactPerson $user */
		$user = $this->contactPersonRepository->findOneBy(['email' => $email]);
		if ($user) {
			return new ErrorResponse(
				Response::HTTP_CONFLICT,
				ApiError::CODE_ENTITY_EXISTS,
				ApiError::$descriptions[ApiError::CODE_ENTITY_EXISTS]
			);
		}
		$createResponse = $this->macroConn->runMacro(
			$this->parameterBag->get('cp.macro_id_create_user'),
			[$dataRequest['customerId']],
			[
				'firstName' => $firstName,
				'lastName' => $lastName,
				'email' => $email,
			]
		);
		if (!$createResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_ERROR,
				ApiError::$descriptions[ApiError::CODE_MACRO_RUN_ERROR]
			);
		}

		$macroStatus = $this->macroConn->checkStatusTilCompleted($createResponse->actionId);

		if (MacroConnector::STATUS_PENDING === $macroStatus) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_STILL_PENDING,
				ApiError::$descriptions[ApiError::CODE_MACRO_RUN_STILL_PENDING]
			);
		}

		if (MacroConnector::STATUS_COMPLETED === $macroStatus) {
			return new ApiResponse(code: Response::HTTP_CREATED);
		}

		return new ErrorResponse(
			Response::HTTP_BAD_GATEWAY,
			ApiError::CODE_MACRO_RUN_ERROR,
			ApiError::$descriptions[ApiError::CODE_MACRO_RUN_ERROR]
		);
	}

	public function processAuthenticatePublicLogin(array $dataRequest): ApiResponse
	{
		$user = $this->contactPersonRepository->findOneBy([
			'publicAuthenticationToken' => strtoupper($dataRequest['token']),
		]);

		if (!$user) {
			return new ErrorResponse(
				Response::HTTP_FORBIDDEN,
				ApiError::CODE_FORBIDDEN_ACTION,
				ApiError::$descriptions[ApiError::CODE_FORBIDDEN_ACTION]
			);
		}

		if (!$user->getSystemAccount()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'system_account');
		}

		if (!$user->getSystemAccount()->getWebLoginAllowed()) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_USER_LOGIN_NOT_ALLOWED,
				ApiError::$descriptions[ApiError::CODE_USER_LOGIN_NOT_ALLOWED]
			);
		}

		$user->setPublicAuthenticationToken();
		$this->em->persist($user);
		$this->em->flush();

		return $this->processLogin(
			params: ['user' => $user, 'ip' => $dataRequest['ip']],
			verifyPass: false
		);
	}

	public function processLoginFromAdminPortal(array $dataRequest): ApiResponse
	{
		$payload = (array) $this->jwtSrv->decode($dataRequest['token']);
		$receivedSubnet = implode('.', array_slice(explode('.', $payload['ip']), 0, 3));
		$currentSubnet = implode('.', array_slice(explode('.', $dataRequest['ip']), 0, 3));
		if ($receivedSubnet !== $currentSubnet) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'token'
			);
		}

		if ($this->jwtSrv->isExpired($payload)) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_TOKEN_EXPIRED,
				ApiError::$descriptions[ApiError::CODE_TOKEN_EXPIRED]
			);
		}

		$user = $this->contactPersonRepository->find($payload['identity']);
		if (!$user) {
			return new ErrorResponse(
				Response::HTTP_FORBIDDEN,
				ApiError::CODE_FORBIDDEN_ACTION,
				ApiError::$descriptions[ApiError::CODE_FORBIDDEN_ACTION]
			);
		}
		$customer = $user->getCustomersPerson()?->getCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		if (!$user->getSystemAccount()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'system_account');
		}

		if (!$user->getSystemAccount()->getWebLoginAllowed()) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_USER_LOGIN_NOT_ALLOWED,
				ApiError::$descriptions[ApiError::CODE_USER_LOGIN_NOT_ALLOWED]
			);
		}

		$internalUser = $this->internalUserRepo->find($payload['ap_identity']);
		if (!$internalUser) {
			return new ErrorResponse(
				Response::HTTP_FORBIDDEN,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'token'
			);
		}

		if (!$internalUser->getCpLoginGodMode()) {
			if (!$internalUser->getCpLoginCustomers() || !in_array($customer->getId(), $internalUser->getCpLoginCustomers())) {
				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_USER_LOGIN_NOT_ALLOWED, 'Admin Portal User can not login with this user.');
			}
		}

		$xtrfSession = $this->retrieveXtrfSessionId($user->getSystemAccount()->getUid());
		if ($xtrfSession instanceof ErrorResponse) {
			return $xtrfSession;
		}
		$this->saveXtrfUserData(
			userId: $user->getUserIdentifier(),
			username: $user->getSystemAccount()?->getUid(),
			xtrfSessionId: $xtrfSession['jsessionid'],
		);

		unset($payload['ttl']);
		$jwt = $this->jwtSrv->generateToken($payload);

		return new ApiResponse(data: [
			'token' => $jwt,
			'refreshToken' => null,
			'user' => Factory::contactPersonDtoInstance($user, $this->baseSecurityHandler->getAbilities($user->getRoles()), $customer, true),
			'customer' => Factory::customerDtoInstance($customer),
		]);
	}

	public function processLogin(array $params, $verifyPass = true): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = null;
		if (!empty($params['username'])) {
			$user = $this->contactPersonRepository->getByUsername($params['username']);
		}

		if (!empty($params['user'])) {
			$user = $params['user'];
		}

		if (!$user) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'username');
		}

		$customer = $this->getCurrentCustomer() ?? $user->getCustomersPerson()?->getCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		if ($verifyPass) {
			if (null === $user->getSystemAccount()->getCpApiPassword()) {
				$codeToken = strtoupper(Random::get(6));
				$user->setRecoveryPassToken($codeToken);
				$user->setLastLoginDate(new \DateTime());
				$this->em->persist($user);
				$this->em->flush();
				$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $user->getEmail(), [
					'subject' => 'AvantPortal One-Time Password',
					'template' => 'ap-otp',
					'data' => 			[
						'name' => $user->getName(),
						'code' => $codeToken,
					],
				]);

				return new ApiResponse(['username' => $user->getSystemAccount()->getUid()], Response::HTTP_UNAUTHORIZED, ApiError::CODE_CHANGE_PASSWORD_REQUIRED, ApiError::$descriptions[ApiError::CODE_CHANGE_PASSWORD_REQUIRED]);
			}

			if (!$this->encoder->isPasswordValid($user, $params['password'])) {
				$user->setLastFailedLoginDate(new \DateTime());
				$this->em->persist($user);
				$this->em->flush();

				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'password');
			}
		}

		$xtrfSession = $this->retrieveXtrfSessionId($user->getSystemAccount()->getUid());
		if ($xtrfSession instanceof ErrorResponse) {
			return $xtrfSession;
		}
		$this->saveXtrfUserData(
			userId: $user->getUserIdentifier(),
			username: $user->getSystemAccount()?->getUid(),
			xtrfSessionId: $xtrfSession['jsessionid'],
		);
		$user
			->setLastLoginDate(new \DateTime())
			->setLastFailedLoginDate(null);
		$this->em->persist($user);
		$this->em->flush();

		if ($verifyPass && $user->getTwoFactorEnabled()) {
			$token = new CPToken();
			$id = $token->getId();
			$token->setUser($user);
			$now = $copy = new \DateTime();
			$token->setCreatedAt($now);
			$token->setExpiresAt($copy->add(new \DateInterval('PT30M')));
			$smsToken = Random::get(6);
			$msg = $this->twilioSrv->send($user->getMobilePhone(), $smsToken);
			$this->loggerSrv->addInfo(json_encode($msg));
			$token->setToken(strtoupper($smsToken));
			$this->em->persist($token);
			$this->em->flush();

			return new ApiResponse(['user' => $id], Response::HTTP_UNAUTHORIZED, ApiError::CODE_TWO_FACTOR_REQUIRED, ApiError::$descriptions[ApiError::CODE_TWO_FACTOR_REQUIRED]);
		}

		$payload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ip' => $params['ip'],
			'identity' => $user->getId(),
			'target' => Permission::TARGET_CLIENT_PORTAL,
            'active_office' => $customer->getId(),
		];
		$jwt = $this->jwtSrv->generateToken($payload);
		$refreshToken = $this->jwtSrv->generateToken($payload, true);

		$roles = array_unique(array_merge($user->getRoles(), Factory::customerRoles($customer)), SORT_REGULAR);
		$user->setRoles($roles);

		return new ApiResponse(data: [
			'token' => $jwt,
			'refreshToken' => $refreshToken,
			'user' => Factory::contactPersonDtoInstance($user, $this->baseSecurityHandler->getAbilities($user->getRoles()), $this->getCurrentCustomer(), true),
			'customer' => Factory::customerDtoInstance($customer),
		]);
	}

	public function processLogout(): ApiResponse
	{
		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processRefreshToken(array $params): ApiResponse
	{
		$decodeToken = (array) $this->jwtSrv->decode($params['token']);
		if (!isset($decodeToken['isRefresh'])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'refresh');
		}
		if ($this->jwtSrv->isExpired($decodeToken)) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_TOKEN_EXPIRED,
				ApiError::$descriptions[ApiError::CODE_TOKEN_EXPIRED]
			);
		}
		$payload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ip' => $decodeToken['ip'],
			'identity' => $decodeToken['identity'],
			'target' => Permission::TARGET_CLIENT_PORTAL,
			'active_office' => $decodeToken['active_office'] ?? null,
		];
		$user = $this->em->getRepository(ContactPerson::class)->find($decodeToken['identity']);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$xtrfSession = $this->retrieveXtrfSessionId($user->getSystemAccount()->getUid());
		if ($xtrfSession instanceof ErrorResponse) {
			return $xtrfSession;
		}
		$this->saveXtrfUserData(
			userId: $user->getUserIdentifier(),
			username: $user->getSystemAccount()?->getUid(),
			xtrfSessionId: $xtrfSession['jsessionid'],
		);
		$jwt = $this->jwtSrv->generateToken($payload);

		return new ApiResponse(data: [
			'token' => $jwt,
			'refreshToken' => $params['token'],
		]);
	}

	public function processRecoveryPasswordInit(array $dataRequest): ApiResponse
	{
		$username = $dataRequest['username'];
		/** @var ContactPerson $user */
		$user = $this->contactPersonRepository->getByUsername($username);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$codeToken = strtoupper(Random::get(6));
		$user->setRecoveryPassToken($codeToken);
		$this->em->persist($user);
		$this->em->flush();
		$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $user->getEmail(), [
			'subject' => 'AvantPortal Reset Password',
			'template' => 'ap-reset',
			'data' => 			[
				'name' => $user->getName(),
				'code' => $codeToken,
			],
		]);

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processPasswordRecoveryConfirm(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->contactPersonRepository->findOneBy(['recoveryPassToken' => $dataRequest['token']]);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$passwordEnc = $this->encoder->hashPassword($user, trim($dataRequest['new_password']));
		$user->getSystemAccount()->setCpApiPassword($passwordEnc);
		$user->setRecoveryPassToken(null);
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function retrieveXtrfSessionId(string $systemAccountUid): ErrorResponse|array
	{
		$responseToken = $this->xtrfConnector->getSingInToken($systemAccountUid);
		if (!$responseToken->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'password');
		}
		$tokenRaw = $responseToken->getRaw();
		if (empty($tokenRaw)) {
			$msg = "Login with token response is empty for $systemAccountUid";
			$this->loggerSrv->addError($msg);

			return new ErrorResponse(Response::HTTP_UNAUTHORIZED, ApiError::CODE_AUTHENTICATION_FAILED, ApiError::$descriptions[ApiError::CODE_AUTHENTICATION_FAILED]);
		}
		$loginResponse = $this->customerPortalConnector->loginWithToken($tokenRaw['token']);
		if (!$loginResponse->isSuccessfull()) {
			$msg = "Unable to get customer session id for $systemAccountUid";
			$this->loggerSrv->addError($msg);

			return new ErrorResponse(Response::HTTP_UNAUTHORIZED, ApiError::CODE_AUTHENTICATION_FAILED, ApiError::$descriptions[ApiError::CODE_AUTHENTICATION_FAILED]);
		}
		$rawData = $loginResponse->getRaw();
		if (empty($rawData)) {
			$msg = "Login with token response is empty for $systemAccountUid";
			$this->loggerSrv->addError($msg);

			return new ErrorResponse(Response::HTTP_UNAUTHORIZED, ApiError::CODE_AUTHENTICATION_FAILED, ApiError::$descriptions[ApiError::CODE_AUTHENTICATION_FAILED]);
		}

		return $rawData;
	}
}
