<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Util\Factory;
use App\Apis\Shared\Util\HolidayProvider;
use App\Constant\SettingsSchema;
use App\Model\Entity\Country;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Entity\CustomFieldConfiguration;
use App\Model\Entity\Project;
use App\Model\Entity\RejectionReason;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Model\Entity\SystemAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Model\Entity\Customer;
use App\Model\Entity\LanguageSpecialization;
use App\Model\Entity\Service;
use App\Model\Entity\XtrfCpConfiguration;
use App\Model\Entity\XtrfLanguage;
use Yasumi\Yasumi;

class ResourcesHandler extends BaseHandler
{
	private SessionInterface $session;
	private CustomerPortalConnector $clientConnector;
	private RequestStack $requestStack;
	private EntityManagerInterface $em;

	public function __construct(
		RequestStack $requestStack,
		CustomerPortalConnector $clientConnector,
		EntityManagerInterface $em
	) {
		parent::__construct($requestStack, $em);
		$this->clientConnector = $clientConnector;
		$this->session = $requestStack->getSession();
		$this->requestStack = $requestStack;
		$this->em = $em;
	}

	public function processGetLanguagesList(?string $customerId, bool $all): ApiResponse
	{
		if ($all) {
			$result = $this->getAllOfficeLanguages();
		} else {
			$customer = $this->getCurrentCustomer();
			if (isset($customerId)) {
				/** @var Customer $customer */
				$customer = $this->em->getRepository(Customer::class)->find($customerId);
			}

			if (!$customer) {
				return new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_CUSTOMER_NOT_FOUND,
					ApiError::$descriptions[ApiError::CODE_CUSTOMER_NOT_FOUND]
				);
			}

			$result = $this->getCustomerLanguages($customer);
		}

		return new ApiResponse(data: $result);
	}

	private function getAllOfficeLanguages(): array
	{
		$officesResponse = $this->processGetOfficesList();
		$offices = $officesResponse->getDataResponse()['data'];
		$allLanguages = [];

		foreach ($offices as $office) {
			$customer = $this->em->getRepository(Customer::class)->find($office['id']);
			if ($customer) {
				$languages = $this->getCustomerLanguages($customer, false);
				$allLanguages = array_merge($allLanguages, $languages['all']);
			}
		}

		$allLanguages = $this->uniqueItems($allLanguages);

		return [
			'top' => null,
			'all' => $allLanguages,
		];
	}

	public function getCustomerLanguages(Customer $customer, bool $top = true): array
	{
		if ($customer->getUseDefaultCustomerLanguages()) {
			$xtrfCpSettings = $this->em->getRepository(XtrfCpConfiguration::class)->find(1);
			if ($xtrfCpSettings->isAllActiveLanguagesAvailable()) {
				$xtrfLanguages = $this->em->getRepository(XtrfLanguage::class)->findBy(['active' => true], ['name' => 'ASC']);
			} else {
				$xtrfLanguages = $xtrfCpSettings->getLanguages();
			}
		} else {
			$xtrfLanguages = $customer->getLanguages();
		}

		$topLanguages = [];
		$languages = [];
		foreach ($xtrfLanguages as $xtrfLanguage) {
			$languages[] = Factory::languageDtoInstance($xtrfLanguage);
		}
		$allIds = array_column($languages, 'id');
		if ($top) {
			$topLanguagesResult = $this->em->getRepository(XtrfLanguage::class)->getTopTargetLanguages($customer->getId(), new \DateTime('-6 months'));
			$filteredTopLangResult = $topLanguagesResult->filter(function (XtrfLanguage $lang) use ($allIds) {
				return in_array($lang->getId(), $allIds);
			});
			foreach ($filteredTopLangResult as $xtrfLanguage) {
				$topLanguages[] = Factory::languageDtoInstance($xtrfLanguage);
			}
		}

		return [
			'top' => $topLanguages,
			'all' => $languages,
		];
	}

	public function processGetServicesList(?string $customerId, bool $all): ApiResponse
	{
		if ($all) {
			$result = $this->getAllOfficeServices();
		} else {
			$customer = $this->getCurrentCustomer();
			if (isset($customerId)) {
				/** @var Customer $customer */
				$customer = $this->em->getRepository(Customer::class)->find($customerId);
			}

			if (!$customer) {
				return new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_CUSTOMER_NOT_FOUND,
					ApiError::$descriptions[ApiError::CODE_CUSTOMER_NOT_FOUND]
				);
			}

			$result = $this->getCustomerServices($customer);
		}

		return new ApiResponse(data: $result);
	}

	private function getAllOfficeServices(): array
	{
		$officesResponse = $this->processGetOfficesList();
		$offices = $officesResponse->getDataResponse()['data'];

		$allServices = [];

		foreach ($offices as $office) {
			$customer = $this->em->getRepository(Customer::class)->find($office['id']);
			if ($customer) {
				$services = $this->getCustomerServices($customer, false);
				$allServices = array_merge($allServices, $services['all']);
			}
		}
		$allServices = $this->uniqueItems($allServices);

		return [
			'top' => null,
			'all' => $allServices,
		];
	}

	private function getCustomerServices(Customer $customer, bool $top = true): array
	{
		if ($customer->getUseDefaultCustomerServices()) {
			/** @var XtrfCpConfiguration $xtrfCpSettings */
			$xtrfCpSettings = $this->em->getRepository(XtrfCpConfiguration::class)->find(1);
			if ($xtrfCpSettings->isAllActiveServicesAvailable()) {
				$xtrfServices = $this->em->getRepository(Service::class)->findBy(['active' => true]);
			} else {
				$xtrfServices = $xtrfCpSettings->getServices();
			}
		} else {
			$xtrfServices = $customer->getServices();
		}

		$topServices = [];
		$services = [];
		foreach ($xtrfServices as $xtrfService) {
			$services[] = Factory::serviceDtoInstance($xtrfService->getService());
		}

		$allIds = array_column($services, 'id');

		if ($top) {
			$topServicesResult = $this->em->getRepository(Service::class)->getTopServices($customer->getId(), new \DateTime('-6 months'));
			$filteredTopServicesResult = $topServicesResult->filter(function (Service $service) use ($allIds) {
				return in_array($service->getId(), $allIds);
			});
			foreach ($filteredTopServicesResult as $xtrfService) {
				$topServices[] = Factory::serviceDtoInstance($xtrfService);
			}
		}

		return [
			'top' => $topServices,
			'all' => $services,
		];
	}

	private function uniqueItems(array $items): array
	{
		$unique = [];
		$ids = [];

		foreach ($items as $item) {
			if (!in_array($item->id, $ids)) {
				$ids[] = $item->id;
				$unique[] = $item;
			}
		}

		usort($unique, fn ($a, $b) => strcmp($a->name, $b->name));

		return $unique;
	}

	public function processGetSpecializationList(?string $customerId = null): ApiResponse
	{
		$customer = $this->getCurrentCustomer();

		if (isset($customerId)) {
			/** @var Customer $customer */
			$customer = $this->em->getRepository(Customer::class)->find($customerId);
		}

		if (!$customer) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_CUSTOMER_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_CUSTOMER_NOT_FOUND]
			);
		}

		if ($customer->getUseDefaultCustomerLanguageSpecializations()) {
			$xtrfCpSettings = $this->em->getRepository(XtrfCpConfiguration::class)->find(1);
			if ($xtrfCpSettings->isAllActiveSpecializationsAvailable()) {
				$languageSpecializations =  $this->em->getRepository(LanguageSpecialization::class)->findBy(['active' => true]);
			} else {
				$languageSpecializations = $xtrfCpSettings->getSpecializations();
			}
		} else {
			$languageSpecializations = $customer->getLanguageSpecializations();
		}

		$result = [];
		foreach ($languageSpecializations as $languageSpecialization) {
			$result[] = Factory::specializationDtoInstance($languageSpecialization);
		}

		return new ApiResponse(data: $result);
	}

	public function processGetPriceprofileList(?string $customerId): ApiResponse
	{
		$customer = $customerId ?? $this->getCurrentCustomer()?->getId();
		$response = $this->clientConnector->getPriceprofileList($customer);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}
		$result = [];
		foreach ($response->getResult() as $item) {
			$result[] = $item->toArray();
		}

		return new ApiResponse(data: $result);
	}

	public function processGetSettingsByCustomer(?string $customerId): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		if (isset($customerId)) {
			$customer = $this->em->getRepository(Customer::class)->find($customerId);
		}

		if (!$customer) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_CUSTOMER_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_CUSTOMER_NOT_FOUND]
			);
		}

		$response = Factory::settingDtoInstance($customer->getSettings());

		return new ApiResponse(data: $response);
	}

	public function processSchema(array $params): ApiResponse
	{
		$types = [
			'TEXT' => 'text',
			'CHECKBOX' => 'checkbox',
			'SELECTION' => 'select',
			'MULTI_SELECTION' => 'taglist',
			'NUMBER' => 'number',
			'DATE' => 'date',
		];

		$scope = $params['scope'] ?? 'PROJECT';

		$customFields = $this->em->getRepository(CustomFieldConfiguration::class)->schema($scope);
		foreach ($customFields as $key => $customField) {
			$customFields[$key] = [
				'type' => $types[$customField['type']],
				'name' => $customField['key'],
				//				'key' => $customField['key'],
				'category' => $customField['description'],
			];
			if ('select' === $customFields[$key]['type'] || 'taglist' === $customFields[$key]['type']) {
				$customFields[$key]['options'] = explode(';', $customField['options']);
			}
		}
		$result = [
			'features' => SettingsSchema::features(),
			'customFields' => $customFields,
		];

		return new ApiResponse(data: $result);
	}

	public function processGetDeadlinePrediction(?string $customerId): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		if (isset($customerId)) {
			$customer = $this->em->getRepository(Customer::class)->find($customerId);
		}
		if (!$customer) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_CUSTOMER_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_CUSTOMER_NOT_FOUND]
			);
		}
		$settings = $customer->getSettings()->getProjectSettings();
		$prediction = $this->em->getRepository(Project::class)->getDeadlinePredictions($customer->getId());

		$result = [
			'options' => $settings->getDeadlineOptions(),
			'prediction' => $prediction ? round($prediction, 2) : null,
			'rush' => $settings->getRushDeadline(),
		];

		$result['holidays'] = $this->getHolidaysInNextThreeMonths();

		return new ApiResponse(data: $result);
	}

	private function getHolidaysInNextThreeMonths(): array
	{
		$today = new \DateTime();
		$threeMonthsFromNow = (clone $today)->modify('+3 months');

		$holidays = Yasumi::create(HolidayProvider::class, (int) date('Y'));

		$holidaysInNextThreeMonths = $holidays->between(
			$today,
			$threeMonthsFromNow
		);

		$result = [];
		foreach ($holidaysInNextThreeMonths as $holiday) {
			$result[] = $holiday->format('Y-m-d');
		}

		return $result;
	}

	public function processGetOfficesList(): ApiResponse
	{
		$user = $this->getCurrentUser();
		$officePlace = $this->getOfficeCurrentUser();
		if (empty($officePlace)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_MANAGE_POLICY_EMPTY,
				ApiError::$descriptions[ApiError::CODE_MANAGE_POLICY_EMPTY]
			);
		}
		$result = [];

		switch ($officePlace) {
			case SystemAccount::OFFICE_ONLY_RELATED:
			case SystemAccount::OFFICE_DEPARTMENT:
			case SystemAccount::OFFICE_OFFICE:
				$customer = $this->getCurrentCustomer();
				$result[] = [
					'id' => $customer->getId(),
					'name' => $customer->getName(),
				];
				break;
			case SystemAccount::OFFICE_ALL_OFFICE:
				$customerPerson = $user->getCustomersPerson();
				if (!$customerPerson) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
				}
				$customers = $customerPerson->getCustomers();
				foreach ($customers as $cust) {
					$result[] = [
						'id' => $cust->getId(),
						'name' => $cust->getName(),
					];
				}
				break;
		}

		return new ApiResponse(data: $result);
	}

	public function processGetCountryList(): ApiResponse
	{
		$result = $this->em->getRepository(Country::class)->getList();

		return new ApiResponse(data: $result);
	}

	public function processGetProvincesByCountry(string $id): ApiResponse
	{
		$country = $this->em->getRepository(Country::class)->find($id);
		if (!$country) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'country');
		}

		$result = [];
		foreach ($country->getProvinces() as $province) {
			$result[] = ['id' => $province->getId(), 'name' => $province->getName()];
		}

		return new ApiResponse(data: $result);
	}

	public function processGetRejectReasonsList(): ApiResponse
	{
		$reasons = $this->em->getRepository(RejectionReason::class)->findBy(['active' => true]);

		$result = [];
		foreach ($reasons as $reason) {
			$result[] = ['id' => $reason->getId(), 'name' => $reason->getName()];
		}

		return new ApiResponse(data: $result);
	}
}
