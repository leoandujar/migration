<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Constant\SettingsSchema;
use App\Linker\Services\RedisClients;
use App\Model\Entity\Category;
use App\Model\Entity\CategorySupportedClasses;
use App\Model\Entity\CPSetting;
use App\Model\Entity\CPSettingInvoice;
use App\Model\Entity\CPSettingProject;
use App\Model\Entity\CPSettingReport;
use App\Model\Repository\CustomFieldConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\CustomerRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class SettingHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private CustomerRepository $customerRepository;
	private CustomFieldConfigurationRepository $customFieldRepository;

	public function __construct(
		CustomerRepository $customerRepository,
		EntityManagerInterface $em,
		CustomFieldConfigurationRepository $customFieldRepository,
	) {
		$this->em = $em;
		$this->customerRepository = $customerRepository;
		$this->customFieldRepository = $customFieldRepository;
	}

	public function processGetByCustomer(array $params): ApiResponse
	{
		$customerId = $params['customer_id'];
		$customer = $this->customerRepository->find($customerId);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		if (!$customer->getSettings()) {
			return $this->processGenerateEmptySettings(['customer_id' => $customerId]);
		}

		return new ApiResponse(data: Factory::settingDtoInstance($customer->getSettings()));
	}

	public function processUpdateByCustomer(array $params): ApiResponse
	{
		$customerId = $params['customer_id'];
		$projectSettingsSent = $params['projects_settings'] ?? null;
		$invoiceSettingsSent = $params['invoices_settings'] ?? null;
		$reportSettingsSent = $params['reports_settings'] ?? null;
		$generalSettingsSent = $params['general_settings'] ?? null;

		$customer = $this->customerRepository->find($customerId);
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}
		$cSettings = $customer->getSettings();

		if (!$cSettings) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'settings');
		}

		if ($projectSettingsSent) {
			$cSettingsProject = $cSettings->getProjectSettings();
			$features = $projectSettingsSent['features'] ?? null;
			if (isset($features['working_files_as_ref_files'])) {
				$cSettingsProject->setWorkingFilesAsRefFiles($features['working_files_as_ref_files']);
			}
			if (isset($features['update_working_files'])) {
				$cSettingsProject->setUpdateWorkingFiles($features['update_working_files']);
			}
			if (isset($features['confirmation_send_by_default'])) {
				$cSettingsProject->setConfirmationSendByDefault($features['confirmation_send_by_default']);
			}
			if (isset($features['download_confirmation'])) {
				$cSettingsProject->setDownloadConfirmation($features['download_confirmation']);
			}
			if (isset($features['deadline_options'])) {
				$cSettingsProject->setDeadlineOptions($features['deadline_options']);
			}
			if (isset($features['duplicate_task'])) {
				$cSettingsProject->setDuplicateTask($features['duplicate_task']);
			}
			if (isset($features['analyze_files'])) {
				$cSettingsProject->setAnalyzeFiles($features['analyze_files']);
			}
			if (isset($features['deadline_prediction'])) {
				$cSettingsProject->setDeadlinePrediction($features['deadline_prediction']);
			}
			if (isset($features['quick_estimate'])) {
				$cSettingsProject->setQuickEstimate($features['quick_estimate']);
			}
			if (isset($features['auto_start'])) {
				$cSettingsProject->setAutostart($features['auto_start']);
			}
			if (isset($features['max_file_size'])) {
				$cSettingsProject->setMaxFileSize($features['max_file_size']);
			}
			if (isset($features['files_queue']) && in_array($features['files_queue'], [
				RedisClients::SESSION_KEY_PROJECTS_QUOTES_NORMAL,
				RedisClients::SESSION_KEY_PROJECTS_QUOTES_HIGH,
				RedisClients::SESSION_KEY_PROJECTS_QUOTES_URGENT,
			])) {
				$cSettingsProject->setFilesQueue($features['files_queue']);
			}
			if (isset($features['categories'])) {
				foreach ($features['categories'] as $categoryId) {
					$category = $this->em->getRepository(Category::class)->find($categoryId);
					if (!$category) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
					}
				}
				$cSettingsProject->setCategories($features['categories']);
			}
			if (isset($features['rush_deadline'])) {
				$cSettingsProject->setRushDeadline($features['rush_deadline']);
			}
			if (isset($features['file_extensions_warning'])) {
				$cSettingsProject->setFileExtensionsWarning($features['file_extensions_warning']);
			}
			if (isset($features['dearchive'])) {
				$cSettingsProject->setDearchive($features['dearchive']);
			}
			if (isset($projectSettingsSent['custom_fields'])) {
				$customFields = $projectSettingsSent['custom_fields'];
				foreach ($customFields as $key => $field) {
					if (isset($field['value'])) {
						$customFields[$key]['value'] = trim($field['value']);
					}
				}
				$cSettingsProject->setCustomFieldsNew($customFields);
			}
			$this->em->persist($cSettings);
		}
		if ($invoiceSettingsSent) {
			$cSettingsInvoice = $cSettings->getInvoiceSettings();
			$features = $invoiceSettingsSent['features'] ?? null;
			if (isset($features['online_payment'])) {
				$cSettingsInvoice->setOnlinePayment($features['online_payment']);
			}
			$this->em->persist($cSettings);
		}
		if ($reportSettingsSent) {
			$cSettingsReport = $cSettings->getReportSettings();
			if (isset($reportSettingsSent['predefined_data'])) {
				$cSettingsReport->setPredefinedData($reportSettingsSent['predefined_data']);
			}
			$this->em->persist($cSettings);
		}
		if ($generalSettingsSent) {
			if (isset($generalSettingsSent['teams_webhook'])) {
				$cSettings->setTeamWebhook($generalSettingsSent['teams_webhook']);
			}
			$this->em->persist($cSettings);
		}

		$this->em->flush();

		return new ApiResponse(data: Factory::settingDtoInstance($cSettings));
	}

	public function processGenerateEmptySettings(array $params): ApiResponse
	{
		$customer = $this->customerRepository->find($params['customer_id']);
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}
		$cSettings = $customer->getSettings();
		if ($cSettings) {
			$cSettingsProject = $cSettings->getProjectSettings();
			$cSettingsInvoice = $cSettings->getInvoiceSettings();
			$cSettingsReport = $cSettings->getReportSettings();

			if ($cSettingsProject && $cSettingsInvoice && $cSettingsReport) {
				return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
			}

			if (!$cSettingsProject) {
				$cSettings->setProjectSettings(
					(new CPSettingProject())
						->setWorkingFilesAsRefFiles(true)
						->setUpdateWorkingFiles(true)
						->setConfirmationSendByDefault(false)
						->setDownloadConfirmation(true)
						->setDeadlineOptions([])
						->setDeadlinePrediction(false)
						->setQuickEstimate(false)
						->setAutostart(false)
						->setDuplicateTask(false)
						->setAnalyzeFiles(true)
						->setMaxFileSize(500)
						->setFilesQueue(RedisClients::SESSION_KEY_PROJECTS_QUOTES_NORMAL)
						->setCustomFieldsNew([])
						->setCategories([])
						->setFileExtensionsWarning([])
						->setDearchive(false)
				);
			}

			if (!$cSettingsInvoice) {
				$cSettings->setInvoiceSettings((new CPSettingInvoice())
					->setOnlinePayment(false));
			}

			if (!$cSettingsReport) {
				$cSettings->setReportSettings(new CPSettingReport());
			}
		} else {
			$cSettings = (new CPSetting())
				->setCustomer($customer)
				->setProjectSettings((new CPSettingProject())
					->setWorkingFilesAsRefFiles(true)
					->setUpdateWorkingFiles(true)
					->setConfirmationSendByDefault(false)
					->setDownloadConfirmation(true)
					->setDeadlineOptions([])
					->setAnalyzeFiles(true)
					->setDuplicateTask(true)
					->setDeadlinePrediction(false)
					->setQuickEstimate(false)
					->setAutostart(false)
					->setMaxFileSize(500)
					->setFilesQueue(RedisClients::SESSION_KEY_PROJECTS_QUOTES_NORMAL)
					->setCustomFieldsNew([])
					->setCategories([])
					->setDearchive(false)
					->setFileExtensionsWarning([]))
				->setInvoiceSettings((new CPSettingInvoice())
					->setOnlinePayment(false))
				->setReportSettings(new CPSettingReport());
		}

		$this->em->persist($cSettings);
		$this->em->flush();

		return new ApiResponse(data: Factory::settingDtoInstance($cSettings));
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

		$customFields = $this->customFieldRepository->schema($scope);
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

		// retrieve categories from the database
		$categories = $this->em->getRepository(CategorySupportedClasses::class)->findBy(['supportedClass' => 'PROJECT']);
		$categoryOptions = [];
		foreach ($categories as $category) {
			if ($category->getCategory()->getActive()) {
				$categoryOptions[] = [
					'value' => $category->getCategory()->getId(),
					'label' => $category->getCategory()->getName(),
				];
			}
		}
		$result = [
			'features' => SettingsSchema::features(),
			'customFields' => $customFields,
		];
		$categoriesIndex = array_search('categories', array_column($result['features']['projects'], 'name'));
		if ($categoriesIndex) {
			$result['features']['projects'][$categoriesIndex]['options'] = $categoryOptions;
		}

		return new ApiResponse(data: $result);
	}
}
