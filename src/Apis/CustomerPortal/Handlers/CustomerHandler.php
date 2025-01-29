<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Connector\Xtrf\Dto\AddressDto;
use App\Connector\Xtrf\XtrfConnector;
use App\Model\Entity\Permission;
use App\Model\Entity\Province;
use App\Model\Repository\ContactPersonRepository;
use App\Model\Repository\CountryRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\JwtService;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomerHandler extends BaseHandler
{
	private LoggerService $loggerSrv;
	private SessionInterface $session;
	private EntityManagerInterface $em;
	private XtrfConnector $xtrfConnector;
	private TokenStorageInterface $tokenStorage;
	private CloudFileSystemService $fileBucketService;
	private CustomerPortalConnector $clientConnector;
	private ContactPersonRepository $contactPersonRepository;
	private CountryRepository $countryRepo;
	private RequestStack $requestStack;
	private ResourcesHandler $resourcesHandler;
	protected JwtService $jwtSrv;

	public function __construct(
		TokenStorageInterface $tokenStorage,
		ContactPersonRepository $contactPersonRepository,
		CustomerPortalConnector $clientConnector,
		XtrfConnector $xtrfConnector,
		CountryRepository $countryRepo,
		CloudFileSystemService $fileBucketService,
		LoggerService $loggerService,
		RequestStack $requestStack,
		JwtService $jwtSrv,
		ResourcesHandler $resourcesHandler,
		EntityManagerInterface $em
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
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
		$this->countryRepo = $countryRepo;

		$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_CP);
		$this->requestStack = $requestStack;
		$this->resourcesHandler = $resourcesHandler;
		$this->jwtSrv = $jwtSrv;
	}

	public function processRetrieve(): ApiResponse
	{
		$customer = $this->getCurrentCustomer();

		return new ApiResponse(data: Factory::customerDtoInstance($customer));
	}

	public function processUpdate(array $dataRequest): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		$cpResponse = $this->xtrfConnector->getCustomer($customer->getId());
		$customerDto = $cpResponse->getCustomerDto();
		$useAddressAsCorrespondenceAddress = $dataRequest['use_address_as_correspondence'] ?? null;
		$name = isset($dataRequest['name']) ? strip_tags($dataRequest['name']) : null;
		$email = isset($dataRequest['email']) ? strip_tags($dataRequest['email']) : null;
		$fax = isset($dataRequest['fax']) ? strip_tags($dataRequest['fax']) : null;
		$www = isset($dataRequest['www']) ? strip_tags($dataRequest['www']) : null;
		$addressCountry = $dataRequest['address_country'] ?? null;
		$addressProvince = $dataRequest['address_province'] ?? null;
		$addressCity = $dataRequest['address_city'] ?? null;
		$addressPostalCode = $dataRequest['address_zip_code'] ?? null;
		$addressAddress = isset($dataRequest['address_address']) ? strip_tags($dataRequest['address_address']) : null;
		$addressAddress2 = isset($dataRequest['address_address2']) ? strip_tags($dataRequest['address_address2']) : null;
		$correspondenceAddressCountry = $dataRequest['correspondence_country'] ?? null;
		$correspondenceAddressProvince = $dataRequest['correspondence_province'] ?? null;
		$correspondenceAddressCity = $dataRequest['correspondence_city'] ?? null;
		$correspondenceAddressPostalCode = $dataRequest['correspondence_zip_code'] ?? null;
		$correspondenceAddressAddress = isset($dataRequest['correspondence_address']) ? strip_tags($dataRequest['correspondence_address']) : null;
		$correspondenceAddressAddress2 = isset($dataRequest['correspondence_address2']) ? strip_tags($dataRequest['correspondence_address2']) : null;
		$phone = $dataRequest['phone'] ?? null;
		$addressPhone2 = $dataRequest['address_phone2'] ?? null;
		$addressPhone3 = $dataRequest['address_phone3'] ?? null;
		$mobilePhone = isset($dataRequest['mobile_phone']) ? strip_tags($dataRequest['mobile_phone']) : null;
		$phones = [];

		$contact = $customerDto->contact;

		if (!empty($name)) {
			$customerDto->name = $name;
			$customer->setName($name);
		}

		if (!empty($email)) {
			$emails['primary'] = $email;
			$contact->setEmails($emails);
			$customer->setAddressEmail($email);
		}

		if (!empty($fax)) {
			$contact->setFax($fax);
			$customer->setAddressFax($fax);
		}

		if (!empty($www)) {
			$websites[] = $www;
			$contact->setWebsites($websites);
			$customer->setAddressWww($www);
		}

		if (!empty($mobilePhone)) {
			$contact->setSms($mobilePhone);
			$customer->setAddressMobilePhone($mobilePhone);
		}

		if (!empty($phone)) {
			$phones[] = $phone;
			$customer->setAddressPhone($phone);
		}

		if (!empty($addressPhone2)) {
			$customer->setAddressPhone2($addressPhone2);
		}

		if (!empty($addressPhone3)) {
			$customer->setAddressPhone3($addressPhone3);
		}

		if (!empty($phones)) {
			$contact->setPhones($phones);
		}

		if (!empty($addressCountry)
			|| !empty($addressProvince)
			|| !empty($addressCity)
			|| !empty($addressPostalCode)
			|| !empty($addressAddress)
			|| !empty($addressAddress2)) {
			$addressDto = $customerDto->billingAddress ?? new AddressDto();

			if (!empty($addressCountry)) {
				$addressDto->countryId = $addressCountry;
				$countryObj = $this->countryRepo->find($addressCountry);
				if ($countryObj) {
					$customer->setAddressCountry($countryObj);
				}
			}

			if (!empty($addressProvince)) {
				$addressDto->provinceId = $addressProvince;
				$provinceObj = $this->em->getRepository(Province::class)->find($addressProvince);
				if ($provinceObj) {
					$customer->setAddressProvince($provinceObj);
				}
			}

			if (!empty($addressCity)) {
				$addressDto->city = $addressCity;
				$customer->setAddressCity($addressCity);
			}

			if (!empty($addressPostalCode)) {
				$addressDto->postalCode = $addressPostalCode;
				$customer->setAddressZipCode($addressPostalCode);
			}

			if (!empty($addressAddress)) {
				$addressDto->addressLine1 = $addressAddress;
				$customer->setAddressAddress($addressAddress);
			}

			if (!empty($addressAddress2)) {
				$addressDto->addressLine2 = $addressAddress2;
				$customer->setAddressAddress2($addressAddress2);
			}

			$customerDto->billingAddress = $addressDto;
		}

		if (!empty($correspondenceAddressCountry)
			|| !empty($correspondenceAddressProvince)
			|| !empty($correspondenceAddressCity)
			|| !empty($correspondenceAddressPostalCode)
			|| !empty($correspondenceAddressAddress)
			|| !empty($useAddressAsCorrespondenceAddress)
			|| !empty($correspondenceAddressAddress2)) {
			$addressDto = $customerDto->correspondenceAddress ?? new AddressDto();

			if (null !== $useAddressAsCorrespondenceAddress) {
				$addressDto->sameAsBillingAddress = $useAddressAsCorrespondenceAddress;
			}

			if (!empty($correspondenceAddressCountry)) {
				$addressDto->countryId = $correspondenceAddressCountry;
				$corresCountryObj = $this->countryRepo->find($correspondenceAddressCountry);
				if ($corresCountryObj) {
					$customer->setCorrespondenceCountry($corresCountryObj);
				}
			}

			if (!empty($correspondenceAddressProvince)) {
				$addressDto->provinceId = $correspondenceAddressProvince;
				$corresProvinceObj = $this->em->getRepository(Province::class)->find($correspondenceAddressProvince);
				if ($corresProvinceObj) {
					$customer->setCorrespondenceProvince($corresProvinceObj);
				}
			}

			if (!empty($correspondenceAddressCity)) {
				$addressDto->city = $correspondenceAddressCity;
				$customer->setCorrespondenceCity($correspondenceAddressCity);
			}

			if (!empty($correspondenceAddressPostalCode)) {
				$addressDto->postalCode = $correspondenceAddressPostalCode;
				$customer->setCorrespondenceZipCode($correspondenceAddressPostalCode);
			}

			if (!empty($correspondenceAddressAddress)) {
				$addressDto->addressLine1 = $correspondenceAddressAddress;
				$customer->setCorrespondenceAddress($correspondenceAddressAddress);
			}

			if (!empty($correspondenceAddressAddress2)) {
				$addressDto->addressLine2 = $correspondenceAddressAddress2;
				$customer->setCorrespondenceAddress2($correspondenceAddressAddress2);
			}

			$customerDto->correspondenceAddress = $addressDto;
		}

		$updateResponse = $this->xtrfConnector->updateCustomer($customerDto);
		if (!$updateResponse->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		$this->em->persist($customer);
		$this->em->flush();

		return new ApiResponse(data: Factory::customerDtoInstance($customer));
	}

	public function processSwitch(array $params): ApiResponse
	{
		$officeListResponse = $this->resourcesHandler->processGetOfficesList();
		$currentCustomer = $this->getCurrentCustomer();
		$customerId = $params['customer_id'];

		if ($officeListResponse instanceof ErrorResponse) {
			return $officeListResponse;
		}
		$officeList = $officeListResponse->getDataResponse()['data'] ?? [];
		if (!count($officeList)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'office_list');
		}

		$exists = false;
		foreach ($officeList as $item) {
			if ($item['id'] === $customerId) {
				$exists = true;
				break;
			}
		}

		if (!$exists) {
			return new ErrorResponse(
				Response::HTTP_FORBIDDEN,
				ApiError::CODE_NOT_ENOUGH_PERMISSIONS,
				ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]
			);
		}

		$user = $this->getCurrentUser();
		$payload = [
			'iat' => (new \DateTime('UTC'))->format('U'),
			'ip' => $params['ip'],
			'identity' => $user->getId(),
			'target' => Permission::TARGET_CLIENT_PORTAL,
			'active_office' => $customerId,
		];
		$jwt = $this->jwtSrv->generateToken($payload);
		$refreshToken = $this->jwtSrv->generateToken($payload, true);

		return new ApiResponse(data: [
			'token' => $jwt,
			'refreshToken' => $refreshToken,
		]);
	}
}
