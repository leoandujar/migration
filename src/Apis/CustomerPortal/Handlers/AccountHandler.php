<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\ContactPerson;
use App\Service\FileSystem\FileSystemService;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\LoggerService;
use App\Connector\Xtrf\XtrfConnector;
use DateTimeZone as DateTimeZoneAlias;
use App\Service\FileSystem\CloudFileSystemService;
use App\Apis\Shared\Http\Error\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Apis\Shared\Handlers\MemberHandler as BaseMemberHandler;
use App\Apis\Shared\Handlers\SecurityHandler as BaseSecurityHandler;

class AccountHandler extends BaseHandler
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
	private FileSystemService $fileSystemSrv;
	private BaseMemberHandler $baseMemberHandler;
	private BaseSecurityHandler $baseSecurityHandler;

	public function __construct(
		TokenStorageInterface $tokenStorage,
		ContactPersonRepository $contactPersonRepository,
		CustomerPortalConnector $clientConnector,
		XtrfConnector $xtrfConnector,
		CloudFileSystemService $fileBucketService,
		LoggerService $loggerService,
		RequestStack $requestStack,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		UserPasswordHasherInterface $encoder,
		BaseMemberHandler $baseMemberHandler,
		BaseSecurityHandler $baseSecurityHandler
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
		$this->fileSystemSrv = $fileSystemSrv;
		$this->baseMemberHandler = $baseMemberHandler;
		$this->baseSecurityHandler = $baseSecurityHandler;
	}

	public function processRetrieve(): ApiResponse
	{
		/* @var ContactPerson $user */
		$user = $this->getCurrentUser();
		$abilities = $this->baseSecurityHandler->getAbilities($user->getRoles());
		$userDto = Factory::contactPersonDtoInstance($user, $abilities, $user->getCustomersPerson()?->getCustomer(), true);
		$customerDto = Factory::customerDtoInstance($this->getCurrentCustomer());

		return new ApiResponse(data: [
			'user' => $userDto,
			'customer' => $customerDto,
		]);
	}

	public function processUpdateTwoFactor(): ApiResponse
	{
		/* @var ContactPerson $user */
		$user = $this->getCurrentUser();

		if (!$user->getMobilePhone()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'phone');
		}

		$user->setTwoFactorEnabled(!$user->getTwoFactorEnabled());
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(
			data: [
				'twoFactorEnabled' => $user->getTwoFactorEnabled(),
			]
		);
	}

	public function processUpdate(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $member */
		$member = $this->getCurrentUser();

		return $this->baseMemberHandler->processUpdate($member, $dataRequest);
	}

	public function processUpdatePicture(array $dataRequest): ApiResponse
	{
		/** @var UploadedFile $image */
		$image = $dataRequest['picture'];
		$result = ['image_data' => null];
		$user = $this->getCurrentUser();
		if (!empty($user->getProfilePicName())) {
			$this->fileBucketService->deleteFile($user->getProfilePicName());
			$user->setProfilePicName(null);
			$this->em->persist($user);
			$this->em->flush();
		}
		$picName = uniqid($user->getId()).'__'.$image->getMimeType();
		try {
			$uploadResult = $this->fileBucketService->uploadImage($image, $picName, 300);
			if (!$uploadResult) {
				return new ErrorResponse(Response::HTTP_SERVICE_UNAVAILABLE, ApiError::CODE_UPLOAD_FILE_ERROR, ApiError::$descriptions[ApiError::CODE_UPLOAD_FILE_ERROR]);
			}

			$result = ['picture' => $this->fileBucketService->getImageBase64($picName)];
			$user->setProfilePicName($picName);
			$this->em->persist($user);
			$this->em->flush();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error updating account profile picture.', $thr);
		}

		return new ApiResponse(data: $result);
	}

	public function processDeletePicture(): ApiResponse
	{
		/* @var ContactPerson $user */
		$user = $this->getCurrentUser();
		if (!empty($user->getProfilePicName())) {
			$this->fileBucketService->deleteFile($user->getProfilePicName());
			$user->setProfilePicName(null);
			$this->em->persist($user);
			$this->em->flush();
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processChangePassword(array $dataRequest): ApiResponse
	{
		/* @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$oldPassword = $dataRequest['old_password'];
		$newPassword = $dataRequest['new_password'];

		if (!$this->encoder->isPasswordValid($user, $oldPassword)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'password');
		}

		$passwordEnc = $this->encoder->hashPassword($user, trim($newPassword));
		$user->getSystemAccount()->setCpApiPassword($passwordEnc);
		$user->getSystemAccount()->setPasswordUpdatedAt(new \DateTime());

		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processUpdatePreferences(array $dataRequest): ApiResponse
	{
		/* @var ContactPerson $user */
		$user = $this->getCurrentUser();
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (isset($dataRequest['timezone'])) {
			$timezoneList = DateTimeZoneAlias::listIdentifiers(DateTimeZoneAlias::PER_COUNTRY, 'US');
			$timezoneData = array_combine($timezoneList, $timezoneList);
			if (!isset($timezoneData[$dataRequest['timezone']])) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'timezone');
			}
		}
		$user->setPreferences($dataRequest);
		$this->em->persist($user);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
