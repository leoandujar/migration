<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\CustomerPerson;
use App\Model\Entity\Role;
use App\Model\Repository\ContactPersonRepository;
use App\Service\RegexService;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Traits\UserResolver;
use App\Model\Repository\CustomerRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private CustomerRepository $customerRepository;
	private ContactPersonRepository $contactPersonRepository;

	public function __construct(
		CustomerRepository $customerRepository,
		EntityManagerInterface $em,
		ContactPersonRepository $contactPersonRepository
	) {
		$this->em = $em;
		$this->customerRepository = $customerRepository;
		$this->contactPersonRepository = $contactPersonRepository;
	}

	public function processGetCustomers(array $params): ApiResponse
	{
		$filters['partialName'] = $params['name'] ?? null;
		$filters['limit'] = $params['limit'] ?? null;
		$filters['blOnly'] = filter_var($params['bl_only'] ?? false, FILTER_VALIDATE_BOOLEAN) ?? false;

		if (isset($params['onboarded'])) {
			$filters['onboarded'] = filter_var($params['onboarded'], FILTER_VALIDATE_BOOLEAN);
		}
		$result = $this->customerRepository->getCustomers($filters);

		return new ApiResponse(data: ['customers' => $result]);
	}

	public function processGetCustomer(string $id): ApiResponse
	{
		$customer = $this->customerRepository->find($id);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		$customerStatus = $customer->getStatus();
		$account = $customer->getSystemAccount();
		$portalAccess = $account?->getWebLoginAllowed();
		$groupAccess = $customer->getUseDefaultUserGroup();
		$services = $customer->getUseDefaultCustomerServices();
		$workflows = $customer->getUseDefaultCustomerServicesWorkflows();
		$languages = $customer->getUseDefaultCustomerLanguages();
		$specializations = $customer->getUseDefaultCustomerLanguageSpecializations();
		$priceProfile = $customer->getCustomerPortalPriceProfile();

		$settings = $customer->getSettings()?->getProjectSettings();

		$ready = 'ACTIVE' === $customerStatus && $portalAccess && $groupAccess;
		$requirements = [];
		if ('ACTIVE' !== $customerStatus) {
			$requirements[] = 'customerStatus';
		}
		if (!$portalAccess) {
			$requirements[] = 'portalAccess';
		}
		if (!$groupAccess) {
			$requirements[] = 'groupAccess';
		}

		$suggestions = [];
		if ($services) {
			$suggestions[] = 'services';
		}
		if ($workflows) {
			$suggestions[] = 'workflows';
		}
		if ($languages) {
			$suggestions[] = 'languages';
		}
		if ($specializations) {
			$suggestions[] = 'specializations';
		}
		if (!$priceProfile) {
			$suggestions[] = 'priceProfile';
		}

		$onboarding = [
			'ready' => $ready,
			'onboarded' => $ready && $settings,
			'requirements' => $requirements,
			'suggestions' => $suggestions,
		];

		return new ApiResponse(data: [
				'customer' => Factory::customerDtoInstance($customer),
				'onboarding' => $onboarding,
		]);
	}

	public function processGetContacts(array $params): ApiResponse
	{
		$customer = $this->customerRepository->find($params['id']);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		$contacts = $customer->getCustomerPersons();
		if (isset($params['onboarding'])) {
			$onboarding = filter_var($params['onboarding'], FILTER_VALIDATE_BOOLEAN);
			$contacts = $contacts->filter(function (CustomerPerson $person) use ($onboarding) {
				return $onboarding === $this->determineOnboarded($person->getContactPerson());
			});
		}

		if (!empty($params['status'])) {
			$contacts = $contacts->filter(function (CustomerPerson $person) use ($params) {
				return $params['status'] === $this->determineStatus($this->determineOnboarded($person->getContactPerson()), (bool) $person->getContactPerson()?->getSystemAccount()?->getCpApiPassword());
			});
		}

		$result = [];
		foreach ($contacts->getValues() as $contact) {
			$contactPerson = $contact->getContactPerson();
			$result[] = Factory::contactPersonDtoInstance($contactPerson);
		}

		return new ApiResponse(data: $result);
	}

	public function processGetRoles(array $params): ApiResponse
	{
		$customer = $this->customerRepository->find($params['id']);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		return new ApiResponse(data: $customer->getRoles());
	}

	public function processAssingRole(array $params): ApiResponse
	{
		$roleList = $params['roles'];

		$customer = $this->customerRepository->find($params['id']);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		foreach ($roleList as $role) {
			$role = $this->em->getRepository(Role::class)->findOneBy(['code' => strtoupper($role)]);
			if (!$role) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'role');
			}
		}
		$customer->setRoles($roleList);
		$this->em->persist($customer);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function determineOnboarded(ContactPerson $contact): bool
	{
		$username = $contact->getSystemAccount()?->getUid();
		$isAllowed = $contact->getSystemAccount()?->getWebLoginAllowed() ?? false;
		$isUsernameEmail = $username && RegexService::match(RegexService::REGEX_TYPE_EMAIL, $username);
		$isUsernameSameAsEmail = $username && strtolower($username) === strtolower($contact->getEmail());

		$canUse = $isAllowed && $contact->getActive();
		$emailReady = $isUsernameSameAsEmail && $isUsernameEmail;

		return $emailReady && $canUse;
	}

	private function determineStatus(bool $ready, bool $migrated): string
	{
		$status = 'inactive';

		if ($ready) {
			$status = 'ready';
			if ($migrated) {
				$status = 'active';
			}
		}

		return $status;
	}
}
