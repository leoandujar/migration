<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Handlers\MemberHandler as BaseMemberHandler;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\Permission;
use App\Model\Entity\Role;
use App\Apis\Shared\Util\UtilsService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Util\PostmarkService;
use ShortCode\Random;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ContactPersonHandler
{
	private JwtService $jwtSrv;
	private PostmarkService $emailSrv;
	private EntityManagerInterface $em;
	private JWTTokenManagerInterface $manager;
	private ParameterBagInterface $parameterBag;
	private ContactPersonRepository $contactPersonRepository;
	private BaseMemberHandler $baseMemberHandler;

	public function __construct(
		JwtService $jwtSrv,
		PostmarkService $emailSrv,
		EntityManagerInterface $em,
		JWTTokenManagerInterface $manager,
		ParameterBagInterface $parameterBag,
		ContactPersonRepository $contactPersonRepository,
		BaseMemberHandler $baseMemberHandler
	) {
		$this->em = $em;
		$this->jwtSrv = $jwtSrv;
		$this->manager = $manager;
		$this->emailSrv = $emailSrv;
		$this->parameterBag = $parameterBag;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->baseMemberHandler = $baseMemberHandler;
	}

	public function processGetContactPersons(array $params): ApiResponse
	{
		$firstName = null;
		$lastName = null;
		$name = $params['name'] ?? null;
		$limit = $params['limit'] ?? null;
		$customerId = $params['customer_id'] ?? null;
		if ($name) {
			$nameData = UtilsService::getFirstAndLastName($name);
			$firstName = $nameData[0];
			$lastName = $nameData[1];
		}
		$result = $this->contactPersonRepository->getContactPersons($firstName, $lastName, $customerId, $params['type'], $limit);

		return new ApiResponse(data: $result);
	}

	public function processGenerateOneTimeToken(array $params): ApiResponse
	{
		$cpUser = $this->contactPersonRepository->find($params['contact_person_id']);

		if (!$cpUser) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$role = $this->em->getRepository(Role::class)->findOneBy(['code' => $params['role']]);

		if (!$role) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
		}

		if (Role::TARGET_CLIENT_PORTAL !== $role->getTarget()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'role');
		}

		$commonPayload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ip' => $params['ip'],
			'identity' => $params['contact_person_id'],
			'target' => Permission::TARGET_ADMIN_PORTAL,
		];

		$jwt = $this->jwtSrv->generateToken($commonPayload);
		$cpUser->setRoles([$role->getCode()]);
		$this->em->persist($cpUser);
		$this->em->flush();

		return new ApiResponse(data: [
			'token' => $jwt,
		]);
	}

	public function processResetPassword(array $params): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->contactPersonRepository->find($params['id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$codeToken = strtoupper(Random::get(6));
		$user->setRecoveryPassToken($codeToken);
		$this->em->persist($user);
		$this->em->flush();
		$tplUserResetPassword = $this->parameterBag->get('app.postmark.tpl.id.cp_user_reset_password');
		if (!$this->emailSrv->sendEmailRemoteTemplate(
			PostmarkService::SENDER_NOTIFICATIONS,
			$user->getEmail(),
			$tplUserResetPassword,
			[
				'username' => $user->getSystemAccount()->getUid(),
				'code' => $codeToken,
			]
		)) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_EMAIL_SEND_ERROR,
				ApiError::$descriptions[ApiError::CODE_EMAIL_SEND_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processGetRoles(array $params): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->contactPersonRepository->find($params['id']);

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		return new ApiResponse(data: $user->getRoles());
	}

	public function processAssingRole(array $params): ApiResponse
	{
		$roleList = $params['roles'];

		$user = $this->contactPersonRepository->find($params['id']);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_UNAUTHORIZED, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		foreach ($roleList as $role) {
			$role = $this->em->getRepository(Role::class)->findOneBy(['code' => strtoupper($role)]);
			if (!$role) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
			}
		}

		if (!in_array(Role::ROLE_CP_BASE, $roleList)) {
			$roleList[] = Role::ROLE_CP_BASE;
		}

		$user->setRoles($roleList);
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processUpdateScope(array $dataRequest): ApiResponse
	{
		$member = $this->contactPersonRepository->find($dataRequest['id']);
		if (!$member) {
			return new ErrorResponse(Response::HTTP_UNAUTHORIZED, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		return $this->baseMemberHandler->processUpdateScope($member->getId(), $dataRequest);
	}
}
