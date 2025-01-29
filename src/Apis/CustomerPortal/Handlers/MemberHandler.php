<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Util\Factory;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Model\Entity\Role;
use App\Service\LoggerService;
use App\Connector\Xtrf\XtrfConnector;
use App\Service\FileSystem\CloudFileSystemService;
use App\Apis\Shared\Http\Error\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use App\Connector\Xtrf\Dto\PersonContactDto;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Connector\Xtrf\Dto\CustomerPersonDto;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use App\Connector\Xtrf\Dto\PersonContactEmailDto;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Apis\Shared\Handlers\MemberHandler as BaseMemberHandler;

class MemberHandler extends BaseHandler
{
	private LoggerService $loggerSrv;
	private SessionInterface $session;
	private EntityManagerInterface $em;
	private XtrfConnector $xtrfConnector;
	private TokenStorageInterface $tokenStorage;
	private CloudFileSystemService $fileBucketService;
	private CustomerPortalConnector $clientConnector;
	private ContactPersonRepository $contactPersonRepository;
	private UserPasswordHasherInterface $encoder;
	private RequestStack $requestStack;
	private BaseMemberHandler $baseMemberHandler;

	public function __construct(
		TokenStorageInterface $tokenStorage,
		ContactPersonRepository $contactPersonRepository,
		CustomerPortalConnector $clientConnector,
		XtrfConnector $xtrfConnector,
		CloudFileSystemService $fileBucketService,
		LoggerService $loggerService,
		RequestStack $requestStack,
		EntityManagerInterface $em,
		UserPasswordHasherInterface $encoder,
		BaseMemberHandler $baseMemberHandler
	) {
		parent::__construct($requestStack, $em);
		$this->tokenStorage = $tokenStorage;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->clientConnector = $clientConnector;
		$this->session = $requestStack->getSession();
		$this->fileBucketService = $fileBucketService;
		$this->loggerSrv = $loggerService;
		$this->em = $em;
		$this->xtrfConnector = $xtrfConnector;
		$this->encoder = $encoder;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);

		$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_CP);
		$this->requestStack = $requestStack;
		$this->baseMemberHandler = $baseMemberHandler;
	}

	public function processRetrieve(string $id): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		$member = $this->contactPersonRepository->find($id);

		if (!$member) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$memberCustomer = $member->getCustomersPerson()->getCustomer();

		if ($memberCustomer->getId() !== $customer->getId()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$userDto = Factory::contactPersonDtoInstance($member, [], $memberCustomer, true);
		$customerDto = Factory::CustomerDtoInstance($this->getCurrentCustomer());

		return new ApiResponse(
			[
				'user' => $userDto,
				'customer' => $customerDto,
			]
		);
	}

	public function processList(): ApiResponse
	{
		$members = $this->getCustomerMembers();
		$result = [];
		foreach ($members as $member) {
			$result[] = Factory::contactPersonDtoInstance($member);
		}

		return new ApiResponse(data: ['members' => $result]);
	}

	public function processCreate(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$customer = $this->getCurrentCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		if (isset($dataRequest['roles'])) {
			foreach ($dataRequest['roles'] as $roleCode) {
				$roleObj = $this->em->getRepository(Role::class)->findOneBy(['code' => $roleCode, 'target' => Role::TARGET_CLIENT_PORTAL]);
				if (!$roleObj) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
				}
			}
		} else {
			$dataRequest['roles'] = [Role::ROLE_CP_BASE];
		}

		/** @var CustomerPersonDto $contactPersonDto */
		$contactEmailDto = (new PersonContactEmailDto())->setPrimary(strip_tags($dataRequest['email']));
		$contactDto = (new PersonContactDto())->setEmails($contactEmailDto);
		$contactPersonDto = (new CustomerPersonDto())
			->setName(strip_tags($dataRequest['name']))
			->setLastName(strip_tags($dataRequest['last_name']))
			->setActive(true)
			->setContact($contactDto);

		$contactPersonDto->setCustomerId($customer->getId());
		$createResponse = $this->xtrfConnector->createCustomerPerson($contactPersonDto);
		if (!$createResponse->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		$contactPersonId = $createResponse->getContactPerson()->id;

		if (isset($dataRequest['roles'])) {
			$contactPersonObj = $this->contactPersonRepository->find($contactPersonId);
			if ($contactPersonObj) {
				$contactPersonObj->setRoles($dataRequest['roles']);
				$this->em->persist($contactPersonObj);
				$this->em->flush();
			}
		}

		return new ApiResponse(
			data: [
				'id' => $contactPersonId,
			]
		);
	}

	public function processUpdateStatus(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$siblingId = $dataRequest['id'];
		$siblingList = $this->getCustomerMembers();
		if (!isset($siblingList[$siblingId])) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}
		$user = $siblingList[$siblingId];
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$cpResponse = $this->xtrfConnector->getCustomerPerson($user->getId());
		$contactPersonDto = $cpResponse->getContactPerson();
		$active = $dataRequest['active'];
		if (null !== $active) {
			$contactPersonDto->setActive(boolval($active));
			$user->setActive(boolval($active));
		}

		$updateResponse = $this->xtrfConnector->updateCustomerPerson($contactPersonDto);
		if (!$updateResponse->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processUpdate(array $dataRequest): ApiResponse
	{
		$memberId = $dataRequest['id'];
		$memberList = $this->getCustomerMembers();
		if (!isset($memberList[$memberId])) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}
		$member = $memberList[$memberId];

		return $this->baseMemberHandler->processUpdate($member, $dataRequest);
	}

	public function processUpdateScope(array $dataRequest): ApiResponse
	{
		$memberId = $dataRequest['id'];
		$memberList = $this->getCustomerMembers();
		if (!isset($memberList[$memberId])) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}
		$member = $memberList[$memberId];

		return $this->baseMemberHandler->processUpdateScope($member->getId(), $dataRequest);
	}

	public function processDelete(string $siblingId): ApiResponse
	{
		$user = $this->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$siblingList = $this->getCustomerMembers();
		if (!isset($siblingList[$siblingId])) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		$deleteResponse = $this->xtrfConnector->deleteCustomerPerson($siblingId);
		if (!$deleteResponse->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
