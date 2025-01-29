<?php

namespace App\Apis\Shared\Util;

use App\Apis\Shared\DTO\FlowActionDto;
use App\Apis\Shared\DTO\ActivityDto;
use App\Apis\Shared\DTO\AnalyticProjectDto;
use App\Apis\Shared\DTO\APFormDto;
use App\Apis\Shared\DTO\APFormSubmissionDto;
use App\Apis\Shared\DTO\APFormTemplateDto;
use App\Apis\Shared\DTO\APTemplateDto;
use App\Apis\Shared\DTO\AVChartDto;
use App\Apis\Shared\DTO\AVDashboardDto;
use App\Apis\Shared\DTO\BlCallDto;
use App\Apis\Shared\DTO\CategoryGroupDto;
use App\Apis\Shared\DTO\ContactPersonBasictDto;
use App\Apis\Shared\DTO\ContactPersonDto;
use App\Apis\Shared\DTO\CurrencyDto;
use App\Apis\Shared\DTO\CustomerAddressDto;
use App\Apis\Shared\DTO\CustomerDto;
use App\Apis\Shared\DTO\CustomerRuleDto;
use App\Apis\Shared\DTO\FeatureProjectDto;
use App\Apis\Shared\DTO\FlowDto;
use App\Apis\Shared\DTO\FlowMonitorDto;
use App\Apis\Shared\DTO\GenericOptionDto;
use App\Apis\Shared\DTO\GenericPersonDto;
use App\Apis\Shared\DTO\InternalUserDto;
use App\Apis\Shared\DTO\InvoiceDto;
use App\Apis\Shared\DTO\LanguageDto;
use App\Apis\Shared\DTO\ParameterDto;
use App\Apis\Shared\DTO\ProjectDto;
use App\Apis\Shared\DTO\QualityCategoryDto;
use App\Apis\Shared\DTO\QualityEvaluationDto;
use App\Apis\Shared\DTO\QualityEvaluationRecordDto;
use App\Apis\Shared\DTO\QualityIssueDto;
use App\Apis\Shared\DTO\QualityReportDto;
use App\Apis\Shared\DTO\QuoteDto;
use App\Apis\Shared\DTO\ReportTemplateDto;
use App\Apis\Shared\DTO\ReportTypeDto;
use App\Apis\Shared\DTO\RoleActionDto;
use App\Apis\Shared\DTO\RoleDto;
use App\Apis\Shared\DTO\SettingDto;
use App\Apis\Shared\DTO\SettingInvoiceDto;
use App\Apis\Shared\DTO\SettingProjectDto;
use App\Apis\Shared\DTO\SettingQuoteDto;
use App\Apis\Shared\DTO\SettingReportDto;
use App\Apis\Shared\DTO\TaskDto;
use App\Apis\Shared\DTO\WorkflowDto;
use App\Apis\Shared\DTO\ServiceDto;
use App\Apis\Shared\Facade\AppFacade;
use App\Constant\DateConstant;
use App\Flow\DTO\ActionInputDto;
use App\Flow\DTO\ActionOutputDto;
use App\Model\Entity\Action;
use App\Model\Entity\Activity;
use App\Model\Entity\AnalyticsProject;
use App\Model\Entity\APForm;
use App\Model\Entity\APFormSubmission;
use App\Model\Entity\APFormTemplate;
use App\Model\Entity\APQualityEvaluation;
use App\Model\Entity\APTemplate;
use App\Model\Entity\AVChart;
use App\Model\Entity\AVCustomerRule;
use App\Model\Entity\AVDashboard;
use App\Model\Entity\AvFlow;
use App\Model\Entity\AvFlowAction;
use App\Model\Entity\AvFlowMonitor;
use App\Model\Entity\AVParameter;
use App\Model\Entity\AVReportTemplate;
use App\Model\Entity\AVReportType;
use App\Model\Entity\BlCall;
use App\Model\Entity\Category;
use App\Model\Entity\CategoryGroup;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\CPSetting;
use App\Model\Entity\CPSettingInvoice;
use App\Model\Entity\CPSettingProject;
use App\Model\Entity\CPSettingQuote;
use App\Model\Entity\CPSettingReport;
use App\Model\Entity\Currency;
use App\Model\Entity\Customer;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\InternalUser;
use App\Model\Entity\Project;
use App\Model\Entity\QualityCategory;
use App\Model\Entity\QualityReport;
use App\Model\Entity\Role;
use App\Model\Entity\Task;
use App\Model\Entity\WFWorkflow;
use App\Model\Entity\XtrfLanguage;
use App\Model\Entity\LanguageSpecialization;
use App\Model\Entity\Quote;
use App\Model\Entity\Service;
use App\Model\Entity\User;
use App\Model\Entity\WorkflowJobFile;
use App\Service\RegexService;

class Factory
{
	public static function activityDtoInstance(Activity $activity): ActivityDto
	{
		$activityDto = new ActivityDto();
		$activityDto
			->setId($activity->getId())
			->setIdNumber($activity->getProjectPhaseIdNumber())
			->setProvider($activity->getProvider()->getName());

		return $activityDto;
	}

	public static function analyticProjectDtoInstance(AnalyticsProject $analytic): AnalyticProjectDto
	{
		return new AnalyticProjectDto(
			id: $analytic->getId(),
			externalId: $analytic->getExternalId(),
			name: $analytic->getName(),
			projectHumanId: $analytic->getProjectHumanId(),
			targetLanguageCode: $analytic->getTargetLanguageCode(),
			status: $analytic->getStatus(),
			processingStatus: $analytic->getProcessingStatus(),
			ignored: $analytic->getIgnored(),
			lqaAllowed: $analytic->getLqaAllowed(),
			lqaProcessed: $analytic->getLqaProcessed(),
			finishDate: $analytic->getFinishDate()->format(DateConstant::GLOBAL_FORMAT),
			activity: [
				'activity_id' => $analytic?->getJob()?->getId(),
				'project_phase_id_number' => $analytic?->getJob()?->getProjectPhaseIdNumber(),
				'activity_name' => $analytic?->getJob()?->getActivityName(),
			],
		);
	}

	public static function categoryGroupDtoInstance(CategoryGroup $categoryGroup): CategoryGroupDto
	{
		$charts = [];
		foreach ($categoryGroup->getCharts() as $chart) {
			$charts[] = self::avChartDtoInstance($chart);
		}

		$workflows = [];
		foreach ($categoryGroup->getWorkflows() as $workflow) {
			$workflows[] = self::workflowDtoInstance($workflow);
		}

		return new CategoryGroupDto(
			id: $categoryGroup->getId(),
			name: $categoryGroup->getName(),
			code: $categoryGroup->getCode(),
			target: $categoryGroup->getTarget(),
			active: $categoryGroup->getActive(),
			charts: $charts,
			workflows: $workflows
		);
	}

	public static function avChartDtoInstance(AVChart $avChart): AVChartDto
	{
		return new AVChartDto(
			id: $avChart->getId(),
			slug: $avChart->getSlug(),
			name: $avChart->getName(),
			description: $avChart->getDescription(),
			category: $avChart->getCategory(),
			type: $avChart->getType(),
			returnY: $avChart->getReturnY(),
			active: $avChart->getActive(),
			size: $avChart->getSize(),
			options: $avChart->getOptions(),
			reportType: $avChart->getReportType()->getId()
		);
	}

	public static function workflowDtoInstance(WFWorkflow $workflow): WorkflowDto
	{
		return new WorkflowDto(
			id: $workflow->getId(),
			name: $workflow->getName(),
			description: $workflow->getDescription(),
			workflowType: $workflow->getType(),
			notificationType: $workflow->getParameters()->getNotificationType(),
			notificationTarget: $workflow->getParameters()->getNotificationTarget(),
			params: $workflow->getParameters()->getParams(),
			runAutomatically: $workflow->isRunAutomatically(),
			lastRunAt: $workflow->getLastRunAt()?->format(DateConstant::GLOBAL_FORMAT),
			runPattern: $workflow->getRunPattern(),
			categoryGroups: $workflow->getCategoryGroups()->getValues()
		);
	}

	public static function reportTypeDtoInstance(AVReportType $reportType): ReportTypeDto
	{
		return new ReportTypeDto(
			id: $reportType->getId(),
			name: $reportType->getName(),
			code: $reportType->getCode(),
			description: $reportType->getDescription(),
			functionName: $reportType->getFunctionName(),
		);
	}

	public static function customerDtoInstance(Customer $customer): CustomerDto
	{
		$accountManagerDTO = $projectManagerDTO = [];
		if (AppFacade::getInstance()->fileSystemSrv) {
			$accountManagerPic = $customer->getInHouseAmResponsible()?->getEntityImage();
			$projectManagerPic = $customer?->getInHousePmResponsible()?->getEntityImage();
			$accountManagerPicData = null;
			$projectManagerPicData = null;
			if (null !== $accountManagerPic) {
				if (!empty($accountManagerPic->getImageData())) {
					$accountManagerPicData = AppFacade::getInstance()->fileSystemSrv->getBase64ImagePngFromResource(
						$accountManagerPic->getImageData()
					);
				}
			}
			if (null !== $projectManagerPic) {
				if (!empty($projectManagerPic->getImageData())) {
					$projectManagerPicData = AppFacade::getInstance()->fileSystemSrv->getBase64ImagePngFromResource(
						$projectManagerPic->getImageData()
					);
				}
			}

			$projectManager = $customer?->getInHousePmResponsible();
			$accountManager = $customer?->getInHouseAmResponsible();
			if (null !== $accountManager) {
				$lastName = explode(' ', $accountManager->getLastName());
				$accountManagerDTO = [
					'id' => $accountManager->getId(),
					'name' => sprintf('%s %s', $accountManager->getFirstName(), $lastName[0]),
					'picture' => $accountManagerPicData,
					'type' => 'Account Manager',
				];
			}

			if (null !== $projectManager) {
				$lastName = explode(' ', $projectManager->getLastName());
				$projectManagerDTO = [
					'id' => $projectManager->getId(),
					'name' => sprintf('%s %s', $projectManager->getFirstName(), $lastName[0]),
					'picture' => $projectManagerPicData,
					'type' => 'Project Manager',
				];
			}
		}

		$parentName = $customer->getParentCustomer()?->getName();
		$customerAddressDto = new CustomerAddressDto(
			$customer->getAddressAddress(),
			$customer->getAddressCity(),
			$customer->getAddressProvince()?->getId(),
			$customer->getAddressCountry()?->getId(),
			$customer->getAddressZipCode(),
			$customer->getAddressAddress2(),
			$customer->getCorrespondenceAddress(),
			$customer->getCorrespondenceCity(),
			$customer->getCorrespondenceProvince()?->getId(),
			$customer->getCorrespondenceCountry()?->getId(),
			$customer->getCorrespondenceZipCode(),
			$customer->getCorrespondenceAddress2(),
			$customer->getUseAddressAsCorrespondence(),
			$customer->getAddressPhone(),
			$customer->getAddressPhone2(),
			$customer->getAddressPhone3(),
			$customer->getAddressFax(),
			$customer->getAddressEmail(),
			$customer->getAddressWww(),
		);

		return new CustomerDto(
			$customer->getId(),
			$customer->getName(),
			$parentName,
			$customerAddressDto,
			[$accountManagerDTO, $projectManagerDTO],
			$customer->getRoles(),
			$customer->getCategoryGroups(),
		);
	}

	public static function apFormDtoInstance(APForm $aPForm): APFormDto
	{
		$createdBy = $aPForm->getCreatedBy();
		$createdByDto = new GenericPersonDto(
			$createdBy->getId(),
			$createdBy->getFirstName(),
			$createdBy->getLastName(),
			$createdBy->getEmail(),
		);

		$template = static::apFormTemplateDtoInstance($aPForm->getTemplate());

		return new APFormDto(
			id: $aPForm->getId(),
			createdBy: $createdByDto,
			approvers: $aPForm->getApprovers(),
			template: $template,
			category: $aPForm->getCategory(),
			name: $aPForm->getName(),
			createdAt: $aPForm->getCreatedAt()->format(DateConstant::GLOBAL_FORMAT),
			pmkTemplateId: $aPForm->getPmkTemplateId()
		);
	}

	public static function apFormTemplateDtoInstance(APFormTemplate $apFormTemplate): APFormTemplateDto
	{
		return new APFormTemplateDto(
			id: $apFormTemplate->getId(),
			name: $apFormTemplate->getName(),
			type: $apFormTemplate->getType(),
			content: $apFormTemplate->getContent()
		);
	}

	public static function apFormSubmissionDtoInstance(APFormSubmission $apFormSubmission): APFormSubmissionDto
	{
		$owner = $apFormSubmission->getOwner();
		$ownerDto = null;

		if ($owner) {
			$ownerDto = new GenericPersonDto(
				$owner->getId(),
				$owner->getFirstName(),
				$owner->getLastName(),
				$owner->getEmail(),
			);
		}

		$approvedBy = null;

		if ($apFormSubmission->getApprovedBy()) {
			$approvedBy = "{$apFormSubmission->getApprovedby()?->getFirstName()} {$apFormSubmission->getApprovedby()?->getLastName()}";
		}

		return new APFormSubmissionDto(
			form: $apFormSubmission->getApForm()?->getName(),
			approvers: $apFormSubmission->getApForm()?->getApprovers(),
			submittedBy: "{$apFormSubmission->getSubmittedBy()?->getFirstName()} {$apFormSubmission->getSubmittedBy()?->getLastName()}",
			approvedBy: $approvedBy,
			owner: $ownerDto,
			collaborators: $apFormSubmission->getCollaborators() ?? [],
			id: $apFormSubmission->getId(),
			status: $apFormSubmission->getStatus(),
			submittedAt: $apFormSubmission->getSubmittedAt()?->format(DateConstant::GLOBAL_FORMAT),
			updatedAt: $apFormSubmission->getUpdatedAt()?->format(DateConstant::GLOBAL_FORMAT),
			submittedData: $apFormSubmission->getSubmittedData()
		);
	}

	public static function blCallDtoInstance(BlCall $blCall): BlCallDto
	{
		return new BlCallDto(
			id: $blCall->getBlReferenceId(),
			date: $blCall->getStartDate()->format(DateConstant::GLOBAL_FORMAT),
			language: $blCall->getBlTargetLanguage()->getEnglishName(),
			requester: $blCall->getRequester() ?: $blCall->getBlContact()->getName(),
			duration: $blCall->getCustomerDuration(),
			amount: $blCall->getCustomerAmount()
		);
	}

	public static function parameterDtoInstance(AVParameter $parameter): ParameterDto
	{
		return new ParameterDto(
			id: $parameter->getId(),
			name: $parameter->getName(),
			scope: $parameter->getScope(),
			value: $parameter->getValue()
		);
	}

	public static function projectDtoInstance(Project $project): ProjectDto
	{
		$startDate = $project?->getStartDate()?->format(DateConstant::GLOBAL_FORMAT);
		$deadline = $project?->getDeadline()?->format(DateConstant::GLOBAL_FORMAT);
		$deliveryDate = $project?->getDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT);
		$closeDate = $project?->getCloseDate()?->format(DateConstant::GLOBAL_FORMAT);
		$confirmationSentDate = $project?->getConfirmationSentDate()?->format(DateConstant::GLOBAL_FORMAT);
		$service = $project?->getService()?->getName();
		$specialization = $project?->getSpecialization()?->getName();
		$currencyDto = null;
		if ($project->getCurrency() instanceof Currency) {
			$currencyDto = new CurrencyDto();
			$currencyDto
				->setName($project->getCurrency()->getName())
				->setSymbol($project->getCurrency()->getSymbol());
		}

		$projectManager = null;
		if (null !== $project->getProjectManager()) {
			$manager = $project->getProjectManager();
			$projectManager = new GenericPersonDto(
				$manager->getId(),
				$manager->getFirstName(),
				$manager->getLastName(),
				$manager->getEmail(),
			);
		}

		return new ProjectDto(
			id: $project->getId(),
			idNumber: $project->getIdNumber(),
			refNumber: $project->getCustomerProjectNumber(),
			name: $project->getName(),
			totalAgreed: strval(UtilsService::amountFormat($project->getTotalAgreed())),
			tmSavings: strval(UtilsService::amountFormat($project->getTmSavings())),
			startDate: $startDate,
			deadline: $deadline,
			deliveryDate: $deliveryDate,
			closeDate: $closeDate,
			status: strtolower($project->getStatus()),
			customerSpecialInstructions: $project->getCustomerSpecialInstructions(),
			costCenter: $project->getCostCenter(),
			currency: $currencyDto,
			confirmationSentDate: $confirmationSentDate,
			service: $service,
			specialization: $specialization,
			rapidFire: $project->getRapidFire(),
			rush: $project->getRush(),
			projectManager: $projectManager,
			surveySent: $project->getSurveySent(),
			archived: !(null === $project->getArchivedAt())
		);
	}

	public static function qualityEvaluationDtoInstance(APQualityEvaluation $qualityEvaluation, bool $withRecords = true): QualityEvaluationDto
	{
		$evaluatee = new GenericPersonDto(
			$qualityEvaluation->getEvaluatee()->getId(),
			$qualityEvaluation->getEvaluatee()->getFirstName(),
			$qualityEvaluation->getEvaluatee()->getLastName(),
			$qualityEvaluation->getEvaluatee()->getEmail(),
		);

		$evaluator = new GenericPersonDto(
			$qualityEvaluation->getEvaluator()->getId(),
			$qualityEvaluation->getEvaluator()->getFirstName(),
			$qualityEvaluation->getEvaluator()->getLastName(),
			$qualityEvaluation->getEvaluator()->getEmail(),
		);

		$records = $qualityEvaluation->getRecords();
		$recordsArray = null;

		if ($withRecords) {
			foreach ($records as $record) {
				$category = $record->getCategory();
				$parentId = $category->getParentCategory()->getId();
				$recordDto = new QualityEvaluationRecordDto(
					$category->getId(),
					$category->getName(),
					$record->getValue(),
					$record->getComment()
				);
				$recordsArray[$parentId][] = $recordDto;
			}
		}

		return new QualityEvaluationDto(
			$qualityEvaluation->getId(),
			$evaluatee,
			$evaluator,
			$qualityEvaluation->getScore(),
			$qualityEvaluation->getCreatedAt()?->format(DateConstant::GLOBAL_FORMAT),
			$qualityEvaluation->getType(),
			$qualityEvaluation->isExcellent(),
			$qualityEvaluation->getComment(),
			$recordsArray
		);
	}

	public static function qualityReportDtoInstance(QualityReport $qualityReport, bool $withIssue = false): QualityReportDto
	{
		$qualityIssues = $qualityReport->getQualityIssues();
		$issueCount = $qualityIssues->count();
		$qualityIssuesArray = null;

		if ($qualityReport) {
			if ($withIssue) {
				foreach ($qualityReport->getQualityIssues() as $qualityIssue) {
					$qualityCategory = $qualityIssue->getQualityCategory();
					$parentId = $qualityCategory->getParentCategory()->getId();
					$qualityIssueDto = new QualityIssueDto(
						$qualityCategory->getId(),
						$qualityCategory->getName(),
						$qualityIssue->getMinor(),
						$qualityIssue->getMajor(),
						$qualityIssue->getCritical(),
						$qualityIssue->getComment()
					);
					$qualityIssuesArray[$parentId][] = $qualityIssueDto;
				}
			}
		}

		return new QualityReportDto(
			id: $qualityReport->getId(),
			activity: $qualityReport->getActivity()->getProjectPhaseIdNumber(),
			prooferName: $qualityReport->getProoferName(),
			pageCount: $qualityReport->getPageCount(),
			format: $qualityReport->getFormat(),
			score: $qualityReport->getScore(),
			status: $qualityReport->getStatus(),
			issueCount: $issueCount,
			qualityIssues: $qualityIssuesArray,
			type: $qualityReport->getType(),
			minorMultiplier: $qualityReport->getMinorMultiplier(),
			majorMultiplier: $qualityReport->getMajorMultiplier(),
			criticalMultiplier: $qualityReport->getCriticalMultiplier()
		);
	}

	public static function reportTemplateDtoInstance(AVReportTemplate $avReportTemplate): ReportTemplateDto
	{
		$chartList = [];
		foreach ($avReportTemplate->getChartList()?->getValues() as $chart) {
			$chartList[] = self::avChartDtoInstance($chart->getChart());
		}

		return new ReportTemplateDto(
			id: $avReportTemplate->getId(),
			name: $avReportTemplate->getName(),
			format: $avReportTemplate->getFormat(),
			charts: $chartList,
			filters: $avReportTemplate->getFilters(),
			predefinedData: $avReportTemplate->getPredefinedData(),
			categoryGroups: $avReportTemplate->getCategoryGroups(),
			template: $avReportTemplate->getTemplate()
		);
	}

	public static function languageDtoInstance(XtrfLanguage $language): LanguageDto
	{
		$languageDto = new LanguageDto();
		$languageDto
			->setId($language->getId())
			->setName($language->getName())
			->setSymbol($language->getSymbol());

		return $languageDto;
	}

	public static function specializationDtoInstance(LanguageSpecialization $specialization): GenericOptionDto
	{
		return new GenericOptionDto(
			$specialization->getId(),
			$specialization->getName()
		);
	}

	public static function serviceDtoInstance(Service $service): ServiceDto
	{
		return new ServiceDto(
			id: (string) $service->getId(),
			name: $service->getName(),
			type: UtilsService::stringToCamelCase($service->getActivityType()?->getName() ?? 'other')
		);
	}

	public static function categoryDtoInstance(Category $category): GenericOptionDto
	{
		return new GenericOptionDto(
			$category->getId(),
			$category->getName(),
		);
	}

	public static function roleDtoInstance(Role $role): RoleDto
	{
		return new RoleDto(
			id: $role->getId(),
			code: $role->getCode(),
			name: $role->getName(),
			target: $role->getTarget(),
			abilities: $role->getAbilities()
		);
	}

	public static function actionDtoInstance(Action $action): RoleActionDto
	{
		return new RoleActionDto(
			id: $action->getId(),
			code: $action->getCode(),
			name: $action->getName(),
			target: $action->getTarget()
		);
	}

	public static function internalUserDtoInstance(InternalUser $internalUser, $abilities = []): InternalUserDto
	{
		return new InternalUserDto(
			id: $internalUser->getId(),
			username: $internalUser->getUsername(),
			firstName: $internalUser->getFirstName(),
			lastName: $internalUser->getLastName(),
			email: $internalUser->getEmail(),
			mobile: $internalUser->getMobile(),
			status: $internalUser->getStatus(),
			allCustomersAccess: $internalUser->getCpLoginGodMode(),
			cpLoginCustomers: $internalUser->getCpLoginCustomers(),
			type: $internalUser->getType(),
			roles: $internalUser->getRoles(),
			position: $internalUser->getPosition(),
			department: $internalUser->getDepartment(),
			tags: $internalUser->getTag(),
			categoryGroups: $internalUser->getCategoryGroups(),
			abilities: $abilities,
			xtrfId: $internalUser->getXtrfUser()?->getId(),
		);
	}

	public static function featureProjectDtoInstance(CPSettingProject $projectSetting): FeatureProjectDto
	{
		return new FeatureProjectDto(
			workingFilesAsRefFiles: $projectSetting->isWorkingFilesAsRefFiles(),
			updateWorkingFiles: $projectSetting->isUpdateWorkingFiles(),
			confirmationSendByDefault: $projectSetting->isConfirmationSendByDefault(),
			downloadConfirmation: $projectSetting->isDownloadConfirmation(),
			deadlineOptions: $projectSetting->getDeadlineOptions(),
			analyzeFiles: $projectSetting->isAnalyzeFiles(),
			duplicateTask: $projectSetting->isDuplicateTask(),
			quickEstimate: $projectSetting->isQuickEstimate(),
			deadlinePrediction: $projectSetting->isDeadlinePrediction(),
			autoStart: $projectSetting->isAutostart(),
			maxFileSize: $projectSetting->getMaxFileSize(),
			filesQueue: $projectSetting->getFilesQueue(),
			rushDeadline: $projectSetting->getRushDeadline(),
			categories: $projectSetting->getCategories(),
			fileExtensionsWarning: $projectSetting->getFileExtensionsWarning(),
			dearchive : $projectSetting->isDearchive()
		);
	}

	public static function settingProjectDtoInstance(CPSettingProject $cpSettingProject): SettingProjectDto
	{
		$featureProjectDto = self::featureProjectDtoInstance($cpSettingProject->getSettings()->getProjectSettings());

		return new SettingProjectDto(
			features: $featureProjectDto,
			customFields: $cpSettingProject->getCustomFieldsNew()
		);
	}

	public static function settingQuoteDtoInstance(CPSettingQuote $cpSettingQuote): SettingQuoteDto
	{
		return new SettingQuoteDto(
			workingFilesAsRefFiles: $cpSettingQuote->getWorkingFilesAsRefFiles(),
			updateWorkingFiles: $cpSettingQuote->getUpdateWorkingFiles(),
			confirmationSendByDefault: $cpSettingQuote->getConfirmationSendByDefault(),
			downloadConfirmation: $cpSettingQuote->getDownloadConfirmation(),
			deadlineOptions: $cpSettingQuote->getDeadlineOptions(),
			analyzeFiles: $cpSettingQuote->getAnalyzeFiles(),
			duplicateTask: $cpSettingQuote->getDuplicateTask(),
			quickEstimate: $cpSettingQuote->getQuickEstimate(),
			deadlinePrediction: $cpSettingQuote->getDeadlinePrediction(),
			customFields: $cpSettingQuote->getCustomFields()
		);
	}

	public static function settingDtoInstance(CPSetting $setting): SettingDto
	{
		return new SettingDto(
			projects: $setting->getProjectSettings() ? self::settingProjectDtoInstance($setting->getProjectSettings()) : null,
			quotes: $setting->getQuoteSettings() ? self::settingQuoteDtoInstance($setting->getQuoteSettings()) : null,
			invoices: $setting->getInvoiceSettings() ? self::settingInvoiceDtoInstance($setting->getInvoiceSettings()) : null,
			reports: $setting->getReportSettings() ? self::settingReportDtoInstance($setting->getReportSettings()) : null,
			general: ['teamsWebhook' => $setting->getTeamWebhook()]
		);
	}

	public static function settingInvoiceDtoInstance(CPSettingInvoice $cpSettingInvoice): SettingInvoiceDto
	{
		$cSettingsInvoice = $cpSettingInvoice->getSettings()->getInvoiceSettings();

		$settingsInvoiceDto = new SettingInvoiceDto();
		$settingsInvoiceDto->setOnlinePayment($cSettingsInvoice->getOnlinePayment());

		return $settingsInvoiceDto;
	}

	public static function settingReportDtoInstance(CPSettingReport $cpSettingReport): SettingReportDto
	{
		$cSettingsReport = $cpSettingReport->getSettings()->getReportSettings();

		return new SettingReportDto(
			predefinedData: $cSettingsReport->getPredefinedData()
		);
	}

	public static function templateDtoInstance(APTemplate $template): APTemplateDto
	{
		return new APTemplateDto(
			id: $template->getId(),
			name: $template->getName(),
			targetEntity: $template->getTargetEntity(),
			data: $template->getData()
		);
	}

	public static function invoiceDtoInstance(CustomerInvoice $invoice, bool $withTasks = false): InvoiceDto
	{
		$currencyDto = null;
		if (null !== $invoice->getCurrency()) {
			$currencyDto = self::currencyDtoInstance($invoice->getCurrency());
		}

		$fullypaidDate = $invoice->getFullyPaidDate()?->format(DateConstant::GLOBAL_FORMAT);
		$dueDate = $invoice->getRequiredPaymentDate()?->format(DateConstant::GLOBAL_FORMAT);
		$finalDate = $invoice->getFinalDate()?->format(DateConstant::GLOBAL_FORMAT);

		$taskDto = [];
		$tasks = $invoice->getTasks();
		if ($withTasks) {
			/** @var Task $task */
			foreach ($tasks as $task) {
				$taskDto[] = self::taskDtoInstance($task);
			}
		}

		/** @var Task $firstTask */
		$firstTask = $tasks->first();
		$projectName = $projectId = $projectNumber = null;
		if ($tasks && $firstTask) {
			/** @var Project $project */
			$project = $firstTask->getProject();
			$projectName = $project->getName();
			$projectId = $project->getId();
			$projectNumber = $project->getIdNumber();
		}

		return new InvoiceDto(
			id: $invoice->getId(),
			idNumber: $invoice->getFinalNumber(),
			status: strtolower($invoice->getPaymentState()),
			fullyPaidDate: $fullypaidDate,
			invoiceNote: $invoice->getInvoiceNote(),
			paidValue: $invoice->getPaidValue(),
			internalStatus: strtolower($invoice->getState()),
			dueDate: $dueDate,
			finalDate: $finalDate,
			totalNetto: $invoice->getTotalNetto(),
			dueAmount: $invoice->getDueAmount(),
			customer: $invoice->getCustomer()->getName(),
			currency: $currencyDto,
			qboId: $invoice->getQboId(),
			tasks: $taskDto,
			projectId : $projectId,
			projectName : $projectName,
			projectNumber : $projectNumber,
		);
	}

	public static function currencyDtoInstance(Currency $currency): CurrencyDto
	{
		$currencyDto = (new CurrencyDto())
			->setName($currency->getName())
			->setSymbol($currency->getSymbol());

		return $currencyDto;
	}

	public static function taskDtoInstance(Task $task): TaskDto
	{
		$actualStartDate = $task->getActualStartDate()?->format(DateConstant::GLOBAL_FORMAT);
		$closeDate = $task->getCloseDate()?->format(DateConstant::GLOBAL_FORMAT);
		$deadline = $task->getDeadline()?->format(DateConstant::GLOBAL_FORMAT);
		$deliveryDate = $task->getDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT);
		$estimatedDeliveryDate = $task->getEstimatedDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT);
		$finalInvoiceDate = $task->getFinalInvoiceDate()?->format(DateConstant::GLOBAL_FORMAT);
		$partialDeliveryDate = $task->getPartialDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT);

		$invoiceId = $task?->getCustomerInvoice()?->getId();
		$invoiceNumber = $task?->getCustomerInvoice()?->getFinalNumber();
		$langSourceDto = $langTargetDto = null;
		if ($task->getSourceLanguage() instanceof XtrfLanguage) {
			$langSourceDto = new LanguageDto();
			$langSourceDto
				->setName($task->getSourceLanguage()->getName())
				->setSymbol($task->getSourceLanguage()->getSymbol());
		}
		if ($task->getTargetLanguage() instanceof XtrfLanguage) {
			$langTargetDto = new LanguageDto();
			$langTargetDto->setName($task->getTargetLanguage()->getName())
				->setSymbol($task->getTargetLanguage()->getSymbol());
		}

		$percentage = $task->getTotalActivities() > 0 ? ($task->getProgressActivities() * 100) / $task->getTotalActivities() : 0;

		return new TaskDto(
			id: $task->getId(),
			activitiesStatus: $task->getActivitiesStatus(),
			actualStartDate: $actualStartDate,
			closeDate: $closeDate,
			confirmedFilesDownloading: $task->getConfirmedFilesDownloading(),
			customerInvoiceId: $invoiceId,
			customerInvoiceNumber: $invoiceNumber,
			deadline: $deadline,
			deliveryDate: $deliveryDate,
			estimatedDeliveryDate: $estimatedDeliveryDate,
			finalInvoiceDate: $finalInvoiceDate,
			invoiceable: $task->getInvoiceable(),
			ontimeStatus: $task->getOntimeStatus(),
			partialDeliveryDate: $partialDeliveryDate,
			projectPhaseIdNumber: $task->getProjectPhaseIdNumber(),
			sourceLanguage: $langSourceDto,
			targetLanguage: $langTargetDto,
			status: $task->getStatus(),
			totalAgreed: strval(UtilsService::amountFormat($task->getTotalAgreed())),
			tmSavings: strval(UtilsService::amountFormat($task->getTmSavings())),
			workingFilesNumber: $task->getWorkingFilesNumber(),
			progress: ['total' => $task->getTotalActivities(), 'percentage' => number_format($percentage, 2)],
			awaitingReview: $task->getTaskForReview()->count() > 0,
		);
	}

	public static function contactPersonDtoInstance(ContactPerson $user, array $abilities = [], ?Customer $office = null, bool $picture = false): ContactPersonDto
	{
		$personPosition = $user->getPersonPosition()?->getName();
		$personDepartment = $user->getPersonDepartment()?->getName();

		$contactPersonBasicDto = new ContactPersonBasictDto(
			$user->getName(),
			$user->getLastName(),
			$personPosition,
			$user->getEmail(),
			$user->getPhone(),
			$user->getAddressPhone2(),
			$user->getAddressPhone3(),
			$user->getMobilePhone(),
			$user->getFax()
		);

		$onboarding = self::determineAccountStatus($user);
		$profilePic = null;
		if ($picture && $user->getProfilePicName() && AppFacade::getInstance()->fileBucketSrv) {
			$profilePic = AppFacade::getInstance()->fileBucketSrv->getImageBase64($user->getProfilePicName());
		}

		return new ContactPersonDto(
			$user->getId(),
			$user->getActive(),
			$contactPersonBasicDto,
			$personDepartment,
			$profilePic,
			$user->getTwoFactorEnabled(),
			$user->getRoles(),
			$abilities,
			$office?->getId(),
			$user->getPreferences(),
			$user->getSystemAccount()?->getUid(),
			$user->getSystemAccount()?->getCustomerContactManagePolicy(),
			$onboarding['status'],
			$user->getLastLoginDate()?->format(DateConstant::GLOBAL_FORMAT),
			$user->getLastFailedLoginDate()?->format(DateConstant::GLOBAL_FORMAT),
			$onboarding,
			$user->getSystemAccount()?->getPasswordUpdatedAt(),
		);
	}

	public static function qualityCategoryDtoInstance(QualityCategory $qualityCategory): QualityCategoryDto
	{
		return new QualityCategoryDto(
			id: $qualityCategory->getId(),
			name: $qualityCategory->getName(),
			weight: $qualityCategory->getWeight(),
			isLeaf: $qualityCategory->getIsLeaf(),
			isOther: $qualityCategory->getIsOther(),
			path: $qualityCategory->getPath(),
			pathDepth: $qualityCategory->getPathDepth(),
			parentName: $qualityCategory->getParentCategory()->getName(),
			parentCategory: $qualityCategory->getParentCategory()
		);
	}

	private static function determineAccountStatus(ContactPerson $contact): array
	{
		$contactname = $contact->getSystemAccount()?->getUid();
		$isAllowed = $contact->getSystemAccount() && $contact->getSystemAccount()->getWebLoginAllowed();
		$migrated = (bool) $contact->getSystemAccount()?->getCpApiPassword();
		$isUsernameEmail = $contactname && RegexService::match(RegexService::REGEX_TYPE_EMAIL, $contactname);
		$isUsernameSameAsEmail = $contactname && strtolower($contactname) === strtolower($contact->getEmail());

		$minimumRoles = in_array('ROLE_CP_BASE_ACCOUNT', $contact->getRoles());
		$adminRoles = in_array('ROLE_CP_ADMIN', $contact->getRoles());

		$canUse = $isAllowed && $contact->getActive();
		$emailReady = $isUsernameSameAsEmail && $isUsernameEmail;
		$status = 'inactive';

		$ready = $emailReady && $canUse;
		if ($canUse) {
			$status = 'pending';
			if ($ready) {
				$status = 'ready';
				if ($migrated) {
					$status = 'active';
				}
			}
		}
		$requirements = [];
		if (!$isUsernameSameAsEmail) {
			$requirements[] = 'usernameMismatch';
		}
		if (!$isUsernameEmail) {
			$requirements[] = 'usernameInvalid';
		}
		if (!$isAllowed) {
			$requirements[] = 'portalAccess';
		}
		if (!$contact->getActive()) {
			$requirements[] = 'contactStatus';
		}
		if (!$minimumRoles && !$adminRoles) {
			$requirements[] = 'rolesRequired';
		}

		$warnings = [];
		if (1 === count($contact->getRoles()) && !$adminRoles && $minimumRoles) {
			$warnings[] = 'rolesMinimum';
		}

		return [
			'ready' => $ready,
			'status' => $status,
			'requirements' => $requirements,
			'warnings' => $warnings,
		];
	}

	public static function customerRoles(Customer $customer): ?array
	{
		$roles = $customer->getRoles();
		$parentCustomer = $customer->getParentCustomer();
		if (null !== $parentCustomer) {
			$roles = array_merge($roles, $parentCustomer->getRoles());
		}

		return $roles;
	}

	public static function avCustomerRuleDtoInstance(AVCustomerRule $avCustomerRule): CustomerRuleDto
	{
		$workflowDto = self::WorkflowDtoInstance($avCustomerRule->getWorkflow());
		$customerDto = self::customerDtoInstance($avCustomerRule->getCustomer());

		return new CustomerRuleDto(
			id: $avCustomerRule->getId(),
			name: $avCustomerRule->getName(),
			event: $avCustomerRule->getEvent(),
			type: $avCustomerRule->getType(),
			filters: $avCustomerRule->getFilters(),
			parameters: $avCustomerRule->getParameters(),
			customer: $customerDto,
			workflow: $workflowDto
		);
	}

	public static function avDashboardDtoInstance(AVDashboard $avDashboard): AVDashboardDto
	{
		return new AVDashboardDto(
			id: $avDashboard->getAvChart()->getId(),
			slug: $avDashboard->getAvChart()->getSlug(),
			name: $avDashboard->getAvChart()->getName() ?? null,
			type: $avDashboard->getAvChart()->getType() ?? null,
			description: $avDashboard->getAvChart()->getDescription() ?? null,
			category: $avDashboard->getAvChart()->getCategory() ?? null,
			options: $avDashboard->getOptions(),
		);
	}

	public static function quoteDtoInstance(Quote $quote, array $data): QuoteDto
	{
		$languagesCombinations = $quote?->getLanguagesCombinations();

		$sourceLanguageIds = [];
		$sourceLanguages = [];
		$targetLanguages = [];
		foreach ($languagesCombinations as $languagesCombination) {
			if (null !== $languagesCombination->getSourceLanguage() && $languagesCombination->getSourceLanguage() instanceof XtrfLanguage) {
				if (!in_array($languagesCombination->getSourceLanguage()->getId(), $sourceLanguageIds, true)) {
					$id = $languagesCombination->getSourceLanguage()->getId();
					$sourceLanguageIds[] = $id;

					$sourceLanguages[] = self::languageDtoInstance($languagesCombination->getSourceLanguage());
				}
			}
			if (null !== $languagesCombination->getTargetLanguage() && $languagesCombination->getTargetLanguage() instanceof XtrfLanguage) {
				$targetLanguages[] = self::languageDtoInstance($languagesCombination->getTargetLanguage());
			}
		}

		$requestedByDto = null;
		if (null !== $quote->getCustomerContactPerson()?->getContactPerson() && $quote->getCustomerContactPerson()->getContactPerson() instanceof ContactPerson) {
			/** @var ContactPerson $requestedBy */
			$requestedBy = $quote->getCustomerContactPerson()->getContactPerson();
			$requestedByDto = new GenericPersonDto(
				$requestedBy->getId(),
				$requestedBy->getName(),
				$requestedBy->getLastName(),
				$requestedBy->getEmail(),
			);
		}

		$projectManagerDTO = null;
		if (null !== $quote->getProjectManager() && $quote->getProjectManager() instanceof User) {
			/** @var User $manager */
			$manager = $quote->getProjectManager();
			$projectManagerDTO = new GenericPersonDto(
				$manager->getId(),
				$manager->getFirstName(),
				$manager->getLastName(),
				$manager->getEmail(),
			);
		}

		$customFields = [
			'rush' => $quote->getRush(),
			'cost_center' => $quote->getCostCenter(),
			'nuid' => $quote->getNuid(),
			'billing_contact' => $quote->getBillingContact(),
			'otn_number' => $quote->getOtnNumber(),
			'pr_acc_status' => $quote->getPrAccStatus(),
			'audience' => $quote->getAudience(),
			'domain' => $quote->getDomain(),
			'function' => $quote->getFunction(),
			'genre' => $quote->getGenre(),
			'purpose' => $quote->getPurpose(),
			'rapid_fire' => $quote->getRapidFire(),
			'send_source' => $quote->isSendSource(),
			'source' => $quote->getSource(),
			'invoice_address' => $quote->getInvoiceAddress(),
			'invoice_notes' => $quote->getInvoiceNotes(),
			'li_provider_name' => $quote->getLiProviderName(),
		];

		$customFields = array_filter($customFields, function ($value) {
			return null !== $value;
		});

		$inputFiles = [];
		/** @var Task $firstTask */
		$firstTask = $quote->getTasks()->first();
		if (null !== $firstTask && $firstTask instanceof Task) {
			foreach ($firstTask->getWorkflowJobFiles() as $file) {
				if (WorkflowJobFile::CATEGORY_WORKFILE === $file->getCategory()) {
					$inputFiles[] = [
						'id' => $file->getId(),
						'name' => $file->getName(),
					];
				}
			}
		}

		return new QuoteDto(
			id: $quote->getId(),
			idNumber: $quote->getIdNumber(),
			refNumber: $quote->getCustomerProjectNumber(),
			name: $quote->getName(),
			totalAgreed: strval(UtilsService::amountFormat($quote->getTotalAgreed())),
			tmSavings: strval(UtilsService::amountFormat($quote->getTmSavings())),
			sourceLanguages: $sourceLanguages,
			targetLanguages: $targetLanguages,
			inputFiles: $inputFiles,
			customFields: $customFields,
			additionalContacts: [],
			startDate: $quote->getStartDate()?->format(DateConstant::GLOBAL_FORMAT),
			deadline: $quote?->getDeadline()?->format(DateConstant::GLOBAL_FORMAT),
			status: strtolower($quote->getStatus()),
			customerSpecialInstructions: $quote->getCustomerSpecialInstructions(),
			currency: self::currencyDtoInstance($quote->getCurrency()),
			service: $quote?->getService()?->getName(),
			projectManager: $projectManagerDTO,
			requestedBy: $requestedByDto,
			awaitingReview: isset($data['countAwaitingReview']) && $data['countAwaitingReview'] > 0,
			projectManagerProfilePic: $data['projectManagerPicData'] ?? null,
			accountManagerProfilePic: $data['accountManagerPicData'] ?? null,
			projectId: $quote->getProject()?->getId(),
			instructions: $quote->getCustomerSpecialInstructions(),
			office: $quote->getCustomer()?->getName(),
			specialization: $quote->getSpecialization()?->getName(),
		);
	}

	public static function FlowDtoInstance(AvFlow $flow): FlowDto
	{
		$inputsOnStart = $flow->getActions()
			->filter(function (AvFlowAction $action) {
				return null !== $action->getInputsOnStart();
			})
			->map(function (AvFlowAction $action) {
				$className = $action->getAction();
				$slug = $action->getSlug();
				$className = str_replace('/', '\\', $className);
				$fullClassName = "App\\Flow\\Actions\\{$className}";
				$inputsOnStart = $fullClassName::ACTION_INPUTS;

				$transformedInputs = [];
				foreach ($inputsOnStart as $key => $input) {
					if (null !== $action->getInputs()[$key]) {
						$input['value'] = $action->getInputs()[$key];
					}
					if ($input['value']['show']) {
						$transformedInputs[] = [
							'required' => $input['required'],
							'fromAction' => $input['fromAction'],
							'type' => $input['type'],
							'description' => $input['description'] ?? '',
							'value' => $input['value']['value'],
							'show' => $input['value']['show'],
							'name' => $key,
							'action' => $slug,
						];
					}
				}

				return $transformedInputs;
			})
			->reduce(function ($carry, $item) {
				return array_merge($carry, $item);
			}, []);

		return new FlowDto(
			id: $flow->getId(),
			name: $flow->getName(),
			description: $flow->getDescription(),
			createdAt: $flow->getCreatedAt()->format(DateConstant::GLOBAL_FORMAT) ?? null,
			updatedAt: $flow->getUpdatedAt()->format(DateConstant::GLOBAL_FORMAT) ?? null,
			deletedAt: ($flow->getDeletedAt()) ? $flow->getDeletedAt()->format(DateConstant::GLOBAL_FORMAT) : '',
			runAutomatically: $flow->getRunAutomatically(),
			lastRunAt: ($flow->getLastRunAt()) ? $flow->getLastRunAt()->format(DateConstant::GLOBAL_FORMAT) : '',
			runPattern: $flow->getRunPattern(),
			params: $flow->getParameters(),
			actions: array_map(fn ($action) => self::flowActionDtoInstance($action), $flow->getActions()->toArray()) ?? [],
			categoryGroup: $flow->getCategoryGroups()?->getValues() ?? [],
			inputsOnStart: $inputsOnStart ?? null,
		);
	}

	public static function flowActionDtoInstance(AvFlowAction $action): FlowActionDto
	{
		$inputsOnStart = null;
		$className = $action->getAction();
		$className = str_replace('/', '\\', $className);
		$fullClassName = "App\\Flow\\Actions\\{$className}";
		if (null !== $action->getInputsOnStart()) {
			try {
				$inputsOnStart = $fullClassName::ACTION_INPUTS;
				foreach ($inputsOnStart as $key => $input) {
					$inputsOnStart[$key]['value'] = $action->getInputs()[$key];
				}
			} catch (\Throwable $th) {
				$inputsOnStart = null;
			}
		}

		return new FlowActionDto(
			id: $action->getId(),
			name: $action->getName(),
			slug: $action->getSlug(),
			description: $action->getDescription(),
			action: $action->getAction(),
			inputs: $action->getInputs(),
			category: $action->getCategory(),
			next: $action->getNext()?->getAction(),
			outputs: [] === $action->getOutputs() ? $fullClassName::ACTION_OUTPUTS : $action->getOutputs(),
			inputsOnStart: $inputsOnStart,
		);
	}

	public static function flowActionInputDtoInstance(array $input): ActionInputDto
	{
		return new ActionInputDto(
			required: $input['required'],
			fromAction: $input['fromAction'],
			type: $input['type'],
			options: $input['options'],
		);
	}

	public static function flowActionOutputDtoInstance(array $output): ActionOutputDto
	{
		return new ActionOutputDto(
			description: $output['description'],
			type: $output['type'],
		);
	}

	public static function flowMonitorDtoInstance(AvFlowMonitor $flowMonitor): FlowMonitorDto
	{
		return new FlowMonitorDto(
			id: $flowMonitor->getId(),
			requestedBy: $flowMonitor->getRequestedBy()->getFullName(),
			flowName: $flowMonitor->getFlow()->getName(),
			status: $flowMonitor->getStatus(),
			requestedAt: $flowMonitor->getRequestedAt()->format(DateConstant::GLOBAL_FORMAT) ?? '',
			startedAt: $flowMonitor->getStartedAt()?->format(DateConstant::GLOBAL_FORMAT),
			finishedAt: $flowMonitor->getFinishedAt()?->format(DateConstant::GLOBAL_FORMAT),
			details: $flowMonitor->getDetails(),
			result: $flowMonitor->getResult(),
			auxiliaryData: $flowMonitor->getAuxiliaryData()
		);
	}
}
