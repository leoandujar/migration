<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\InternalUserDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Apis\Shared\Util\JwtUtil;
use App\Apis\Shared\Util\PostmarkService;
use App\Apis\Shared\Util\UtilsService;
use App\Linker\Services\RedisClients;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\InternalUser;
use App\Model\Entity\Permission;
use App\Model\Entity\Role;
use App\Model\Repository\ContactPersonRepository;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\PermissionsRepository;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use ShortCode\Random;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Apis\Shared\Handlers\SecurityHandler as BaseSecurityHandler;

class SecurityHandler
{
	use UserResolver;

	private JwtUtil $jwtUtil;
	private UserHandler $userHandler;
	private PostmarkService $emailSrv;
	private EntityManagerInterface $em;
	private RedisClients $redisClients;
	private ParameterBagInterface $parameterBag;
	private UserPasswordHasherInterface $encoder;
	private ContactPersonRepository $contactPersonRepository;
	private InternalUserRepository $internalUserRepository;
	private PermissionsRepository $permissionsRepository;
	private RequestStack $requestStack;
	private JwtService $jwtSrv;
	private ParameterBagInterface $bag;
	private BaseSecurityHandler $baseSecurityHandler;

	public function __construct(
		ParameterBagInterface $parameterBag,
		JwtUtil $jwtUtil,
		RequestStack $requestStack,
		PermissionsRepository $permissionsRepository,
		EntityManagerInterface $em,
		InternalUserRepository $internalUserRepository,
		ContactPersonRepository $contactPersonRepository,
		PostmarkService $emailSrv,
		UserPasswordHasherInterface $encoder,
		UserHandler $userHandler,
		RedisClients $redisClients,
		JwtService $jwtSrv,
		ParameterBagInterface $bag,
		BaseSecurityHandler $baseSecurityHandler
	) {
		$this->parameterBag = $parameterBag;
		$this->redisClients = $redisClients;
		$this->jwtUtil = $jwtUtil;
		$this->internalUserRepository = $internalUserRepository;
		$this->encoder = $encoder;
		$this->permissionsRepository = $permissionsRepository;
		$this->em = $em;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->emailSrv = $emailSrv;
		$this->userHandler = $userHandler;
		$this->requestStack = $requestStack;
		$this->jwtSrv = $jwtSrv;
		$this->bag = $bag;
		$this->baseSecurityHandler = $baseSecurityHandler;
	}

	public function processPublicLogin(array $params): ApiResponse
	{
		/** @var ContactPerson $contactPerson */
		$contactPerson = $this->contactPersonRepository->find($params['id']);
		if (!$contactPerson) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'person');
		}

		$username = $contactPerson->getUsername();
		$firstName = $contactPerson->getName();
		$lastName = $contactPerson->getLastName() ?? '';
		$email = $contactPerson->getEmail();
		$roles = [Role::ROLE_AP_PUBLIC];

		if (empty($email)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'email');
		}

		/** @var InternalUser $internalUser */
		$internalUser = $this->internalUserRepository->findOneBy(['email' => $email]);

		$tplEmailId = $this->parameterBag->get('app.postmark.tpl_id.pub_login');
		$code = strtoupper(Random::get(10));

		if (InternalUser::TYPE_INTERNAL === $internalUser?->getType()) {
			$internalUser->setLastLoginDate(new \DateTime());
			$this->em->persist($internalUser);
			$this->em->flush();

			return new ApiResponse(data: Factory::internalUserDtoInstance($internalUser));
		}

		if (!$this->emailSrv->sendEmailRemoteTemplate(PostmarkService::SENDER_NOTIFICATIONS, $email, $tplEmailId, ['name' => $firstName, 'code' => $code])) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}
		if ($internalUser) {
			$internalUser->setPassword($this->encoder->hashPassword($internalUser, $code));
			$internalUser->setLastLoginDate(new \DateTime());
			$this->em->persist($internalUser);
			$this->em->flush();

			return new ApiResponse(data: Factory::internalUserDtoInstance($internalUser));
		}

		return $this->userHandler->processCreate(
			[
				'username' => $username,
				'password' => $code,
				'first_name' => $firstName,
				'last_name' => $lastName,
				'email' => $email,
				'roles' => $roles,
				'type' => InternalUser::TYPE_PUBLIC,
				'status' => InternalUser::STATUS_ACTIVE,
				'last_login' => new \DateTime(),
			]
		);
	}

	public function processLogin(array $params): ApiResponse
	{
		$user = null;
		if (!empty($params['username'])) {
			$user = $this->internalUserRepository->findOneBy(['username' => $params['username']]);
		}

		if (!empty($params['upn'])) {
			$user = $this->internalUserRepository->findOneBy(['email' => $params['upn']]);

			if (!$user) {
				$nameData = UtilsService::getFirstAndLastName($params['name']);
				$firstName = $nameData[0] ?? '';
				$lastName = $nameData[1] ?? '';
				$roles = [Role::ROLE_AP_BASE];

				$createResponse = $this->userHandler->processCreate(
					[
						'username' => $params['upn'],
						'first_name' => $firstName,
						'last_name' => $lastName,
						'email' => $params['upn'],
						'roles' => $roles,
						'password' => uniqid($params['upn'], true),
						'status' => InternalUser::STATUS_ACTIVE,
						'department' => $params['department'] ?? null,
						'position' => $params['jobTitle'] ?? null,
						'mobile' => $params['mobile'] ?? null,
					]
				);

				if (!$createResponse instanceof ApiError) {
					$data = $createResponse->getDataResponse();
					if (!empty($data['data'])) {
						$userDto = array_shift($data['data']);
						if ($userDto instanceof InternalUserDto) {
							$user = $this->internalUserRepository->find($userDto->id);
						}
					}
				}
			}
			$user->setType(InternalUser::TYPE_INTERNAL);
		}

		if (!$user) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'username');
		}

		if (InternalUser::STATUS_INACTIVE === $user->getStatus()) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INACTIVE_ENTITY, ApiError::$descriptions[ApiError::CODE_INACTIVE_ENTITY]);
		}

		if (empty($params['upn']) && !$this->encoder->isPasswordValid($user, $params['password'])) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'password');
		}

		if (!empty($params['jobTitle'])) {
			$user->setPosition($params['jobTitle']);
		}

		if (!empty($params['department'])) {
			$user->setDepartment($params['department']);
		}

		if (!empty($params['mobile'])) {
			$user->setMobile($params['mobile']);
		}

		$payload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ip' => $params['ip'],
			'identity' => $user->getId(),
			'target' => Permission::TARGET_ADMIN_PORTAL,
		];

		$jwt = $this->jwtSrv->generateToken($payload);

		$user->setLastLoginDate(new \DateTime());
		$this->em->persist($user);
		$this->em->flush();

		$refreshToken = $this->jwtSrv->generateToken($payload, true);
		$abilities = $this->baseSecurityHandler->getAbilities($user->getRoles());
		$userResponse = Factory::internalUserDtoInstance($user, $abilities);

		return new ApiResponse(
			data: [
				'token' => $jwt,
				'refreshToken' => $refreshToken,
				'user' => $userResponse,
				'ref' => $params['ref'] ?? null,
			]
		);
	}

	public function processCpLogin(array $params): ApiResponse
	{
		/** @var ContactPerson $contactPerson */
		$contactPerson = $this->contactPersonRepository->find($params['id']);
		if (!$contactPerson) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'person');
		}

		if (!$contactPerson->getSystemAccount()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'system_account');
		}

		if (!$contactPerson->getSystemAccount()->getWebLoginAllowed()) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_USER_LOGIN_NOT_ALLOWED,
				ApiError::$descriptions[ApiError::CODE_USER_LOGIN_NOT_ALLOWED]
			);
		}
		/** @var InternalUser $internalUser */
		$internalUser = $this->getCurrentUser();
		if (!$internalUser) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'token');
		}

		if (!$internalUser->getCpLoginGodMode()) {
			if (!$internalUser->getCpLoginCustomers()) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_USER_NEEDS_CUSTOMERS, ApiError::$descriptions[ApiError::CODE_USER_NEEDS_CUSTOMERS]);
			}
			if (!in_array($contactPerson->getCustomersPerson()?->getCustomer()?->getId(), $internalUser->getCpLoginCustomers())) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_ALLOWED_TO_LOGIN, ApiError::$descriptions[ApiError::CODE_NOT_ALLOWED_TO_LOGIN]);
			}
		}

		$payload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ttl' => 300,
			'identity' => $contactPerson->getId(),
            'active_office' => $contactPerson->getCustomersPerson()->getCustomer()->getId(),
			'ap_identity' => $internalUser->getId(),
			'from_ap' => true,
			'ip' => $params['ip'],
			'target' => Permission::TARGET_CLIENT_PORTAL,
		];
		$jwt = $this->jwtSrv->generateToken($payload);

		return new ApiResponse(
			data: ['token' => $jwt]
		);
	}

	public function getCurrentUser(): ?InternalUser
	{
		$sentToken = $this->jwtSrv->extract();
		$payload = (array) $this->jwtSrv->decode($sentToken);

		if (!is_array($payload)) {
			return null;
		}

		return $this->internalUserRepository->findOneBy(['id' => $payload['identity']]);
	}

	public function processLogout(Request $request): ApiResponse
	{
		$user = $this->getCurrentUser($request);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'token');
		}
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDestroy(Request $request): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->getCurrentUser($request);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (InternalUser::TYPE_PUBLIC !== $user->getType()) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::DESTROY_TYPE_INVALID, ApiError::$descriptions[ApiError::DESTROY_TYPE_INVALID]);
		}

		$this->em->remove($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processRefreshToken(array $dataSent): ApiResponse
	{
		$decodeToken = (array) $this->jwtSrv->decode($dataSent['token']);
		$commonPayload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ip' => $decodeToken['ip'],
			'identity' => $decodeToken['identity'],
			'target' => Permission::TARGET_ADMIN_PORTAL,
		];
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
		$jwt = $this->jwtSrv->generateToken($commonPayload);

		return new ApiResponse(
			data: [
				'token' => $jwt,
				'refreshToken' => $dataSent['token'],
			]
		);
	}
}
