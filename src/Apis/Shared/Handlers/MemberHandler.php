<?php

namespace App\Apis\Shared\Handlers;

use App\Apis\Shared\Handlers\UtilsHandler as BaseUtilsHandler;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\SystemAccount;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\LoggerService;
use App\Connector\Xtrf\XtrfConnector;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberHandler
{
	use UserResolver;

	private LoggerService $loggerSrv;
	private SessionInterface $session;
	private EntityManagerInterface $em;
	private XtrfConnector $xtrfConnector;
	private ContactPersonRepository $contactPersonRepository;
	private UserPasswordHasherInterface $encoder;
	private RequestStack $requestStack;
	private BaseUtilsHandler $baseUtilsHandler;

	public function __construct(
		ContactPersonRepository $contactPersonRepository,
		XtrfConnector $xtrfConnector,
		RequestStack $requestStack,
		EntityManagerInterface $em,
		BaseUtilsHandler $baseUtilsHandler
	) {
		$this->contactPersonRepository = $contactPersonRepository;
		$this->session = $requestStack->getSession();
		$this->em = $em;
		$this->xtrfConnector = $xtrfConnector;
		$this->requestStack = $requestStack;
		$this->baseUtilsHandler = $baseUtilsHandler;
	}

	public function processUpdate(ContactPerson $user, array $dataRequest): ApiResponse
	{
		$cpResponse = $this->xtrfConnector->getCustomerPerson($user->getId());
		$contactPersonDto = $cpResponse->getContactPerson();
		$name = isset($dataRequest['name']) ? strip_tags($dataRequest['name']) : null;
		$lastName = isset($dataRequest['last_name']) ? strip_tags($dataRequest['last_name']) : null;
		$email = isset($dataRequest['email']) ? strip_tags($dataRequest['email']) : null;
		$phone = $dataRequest['phone'] ?? null;
		$addressPhone2 = $dataRequest['address_phone2'] ?? null;
		$addressPhone3 = $dataRequest['address_phone3'] ?? null;
		$sms = isset($dataRequest['mobile_phone']) ? strip_tags($dataRequest['mobile_phone']) : null;
		$fax = isset($dataRequest['fax']) ? strip_tags($dataRequest['fax']) : null;
		$phones = [];

		$contact = $contactPersonDto->contact;

		$contactEmail = $contact->emails;

		if (!empty($name)) {
			$contactPersonDto->setName($name);
			$user->setName($name);
		}
		if (!empty($lastName)) {
			$contactPersonDto->setLastName($lastName);
			$user->setLastName($lastName);
		}

		if (!empty($email)) {
			$contactEmail->setPrimary($email);
			$user->setEmail($email);
		}

		if (!empty($phone)) {
			$phones[] = $phone;
			$user->setPhone($phone);
		}
		if (!empty($addressPhone2)) {
			$phones[] = $addressPhone2;
			$user->setAddressPhone2($addressPhone2);
		}
		if (!empty($addressPhone3)) {
			$phones[] = $addressPhone3;
			$user->setAddressPhone3($addressPhone3);
		}

		if (!empty($phones)) {
			$contact->setPhones($phones);
		}

		if (!empty($sms)) {
			$contact->setSms($sms);
			$user->setMobilePhone($sms);
		}
		if (!empty($fax)) {
			$contact->setFax($fax);
			$user->setFax($fax);
		}

		$updateResponse = $this->xtrfConnector->updateCustomerPerson($contactPersonDto);
		if (!$updateResponse->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
		}

		$this->em->persist($user);
		$this->em->flush();
		$userDto = Factory::contactPersonDtoInstance($user, [], $user->getCustomersPerson()->getCustomer(), true);
		$customerDto = Factory::customerDtoInstance($this->getCurrentCustomer());

		return new ApiResponse(
			data: [
				'user' => $userDto,
				'customer' => $customerDto,
			]
		);
	}

	public function processUpdateScope(string $id, array $dataRequest): ApiResponse
	{
		$user = $this->em->getRepository(ContactPerson::class)->find($id);

		if (!$user) {
			return new ErrorResponse(
				Response::HTTP_NOT_FOUND,
				ApiError::CODE_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
				'user'
			);
		}

		if (SystemAccount::OFFICE_ALL_OFFICE_RELATED === $dataRequest['scope']) {
			$xtrfScope  = SystemAccount::OFFICE_ALL_OFFICE;
		}

		$params['macro_id'] = 275;
		$params['ids'] = [$id];
		$params['params'] = [
			'ccp' => [
				$id => [
					'scope' => $xtrfScope ?? $dataRequest['scope'],
					'allow' => $dataRequest['allow'],
				],
			],
		];

		$macro = $this->baseUtilsHandler->processMacro($params);
		$response = json_decode($macro->getContent());
		if (!isset($response->data)) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_ERROR,
				ApiError::$descriptions[ApiError::CODE_MACRO_RUN_ERROR]
			);
		}

		$systemAccount = $user->getSystemAccount();
		$systemAccount->setCpScope($dataRequest['scope']);
		$this->em->persist($systemAccount);
		$this->em->flush();

		$contacts = $response?->data?->contacts;

		return new ApiResponse(
			data: $contacts[0] ?? [],
		);
	}
}
