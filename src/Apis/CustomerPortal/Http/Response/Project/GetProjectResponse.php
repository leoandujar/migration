<?php

declare(strict_types=1);

namespace App\Apis\CustomerPortal\Http\Response\Project;

use App\Apis\Shared\Util\UtilsService;
use App\Model\Entity\Feedback;
use App\Model\Entity\Task;
use App\Model\Entity\User;
use App\Model\Entity\Project;
use App\Model\Entity\Currency;
use App\Apis\Shared\DTO\TaskDto;
use App\Model\Entity\XtrfLanguage;
use App\Apis\Shared\DTO\ProjectDto;
use App\Model\Entity\ContactPerson;
use App\Apis\Shared\DTO\CurrencyDto;
use App\Apis\Shared\DTO\FeedbackDto;
use App\Apis\Shared\DTO\LanguageDto;
use App\Apis\Shared\DTO\GenericPersonDto;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Constant\DateConstant;

class GetProjectResponse extends ApiResponse
{
	/**
	 * GetProjectResponse constructor.
	 */
	public function __construct(mixed $data = null)
	{
		parent::__construct();
		$this->updateData($data);
	}

	public function marshall(mixed $data = null): array
	{
		/** @var Project $project */
		$project = $data['project'];
		$list = $data['tasks'];
		$inputFiles = $data['inputFiles'] ?? [];
		$tasks = [];

		$languagesCombinations = $project->getLanguagesCombinations();

		$startDate = $project->getStartDate()?->format(DateConstant::GLOBAL_FORMAT);
		$deadline = $project->getDeadline()?->format(DateConstant::GLOBAL_FORMAT);
		$deliveryDate = $project->getDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT);
		$closeDate = $project->getCloseDate()?->format(DateConstant::GLOBAL_FORMAT);
		$confirmationSentDate = $project->getConfirmationSentDate()?->format(DateConstant::GLOBAL_FORMAT);

		$service = $project->getService()?->getName();
		$specialization = $project->getSpecialization()?->getName();
		$feedbacks = $project->getFeedbacks();

		$currencyDto = null;
		if ($project->getCurrency() instanceof Currency) {
			$currencyDto = new CurrencyDto();
			$currencyDto
				->setName($project->getCurrency()->getName())
				->setSymbol($project->getCurrency()->getSymbol());
		}

		$customFields = [
			'rush' => $project->getRush(),
			'cost_center' => $project->getCostCenter(),
			'nuid' => $project->getNuid(),
			'billing_contact' => $project->getBillingContact(),
			'otn_number' => $project->getOtnNumber(),
			'pr_acc_status' => $project->getPrAccStatus(),
			'audience' => $project->getAudience(),
			'domain' => $project->getDomain(),
			'function' => $project->getFunction(),
			'genre' => $project->getGenre(),
			'purpose' => $project->getPurpose(),
			'rapid_fire' => $project->getRapidFire(),
			'send_source' => $project->isSendSource(),
			'source' => $project->getSource(),
			'invoice_address' => $project->getInvoiceAddress(),
			'invoice_notes' => $project->getInvoiceNotes(),
			'li_provider_name' => $project->getLiProviderName(),
		];

		$customFields = array_filter($customFields, function ($value) {
			return null !== $value;
		});

		$additionalContacts = [];
		foreach ($project->getCustomerPersons() as $contactPerson) {
			$additionalContacts[] = [
				'id' => $contactPerson->getContactPerson()->getId(),
				'name' => $contactPerson->getContactPerson()->getName(),
				'lastName' => $contactPerson->getContactPerson()->getLastName(),
			];
		}

		$percentage = $project->getTotalActivities() > 0 ? ($project->getProgressActivities() * 100) / $project->getTotalActivities() : 0;
		$projectProgress = [
			'total' => $project->getTotalActivities(),
			'percentage' => number_format($percentage, 2),
		];

		$projectManagerDto = null;
		if ($project->getProjectManager() instanceof User) {
			/** @var User $manager */
			$manager = $project->getProjectManager();
			$projectManagerDto = new GenericPersonDto(
				$manager->getId(),
				$manager->getFirstName(),
				$manager->getLastName(),
				$manager->getEmail(),
			);
		}
		$requestedByDto = null;
		if (null !== $project->getCustomerContactPerson() && $project->getCustomerContactPerson()->getContactPerson() instanceof ContactPerson) {
			/** @var ContactPerson $requestedBy */
			$requestedBy = $project->getCustomerContactPerson()->getContactPerson();
			$requestedByDto = new GenericPersonDto(
				$requestedBy->getId(),
				$requestedBy->getName(),
				$requestedBy->getLastName(),
				$requestedBy->getEmail(),
			);
		}
		$sourceLanguageIds = [];
		$sourceLanguages = $targetLanguages = [];
		foreach ($languagesCombinations as $languagesCombination) {
			if (null !== $languagesCombination->getSourceLanguage() && $languagesCombination->getSourceLanguage() instanceof XtrfLanguage) {
				if (!in_array($languagesCombination->getSourceLanguage()->getId(), $sourceLanguageIds)) {
					$id = $languagesCombination->getSourceLanguage()->getId();
					$sourceLanguageIds[] = $id;
					$langSourceDto = new LanguageDto();
					$langSourceDto
						->setId($id)
						->setName($languagesCombination->getSourceLanguage()->getName())
						->setSymbol($languagesCombination->getSourceLanguage()->getSymbol());
					$sourceLanguages[] = $langSourceDto;
				}
			}
			if (null !== $languagesCombination->getTargetLanguage() && $languagesCombination->getTargetLanguage() instanceof XtrfLanguage) {
				$langtargetDto = new LanguageDto();
				$langtargetDto
					->setId($languagesCombination->getTargetLanguage()->getId())
					->setName($languagesCombination->getTargetLanguage()->getName())
					->setSymbol($languagesCombination->getTargetLanguage()->getSymbol());
				$targetLanguages[] = $langtargetDto;
			}
		}

		$projectFeedbacks = [];
		foreach ($feedbacks as $feedback) {
			if ('CUSTOMER_CLAIM' === $feedback->getFeedbackType()) {
				$feedbackDto = new FeedbackDto(
					$feedback->getId(),
					$feedback->getCreationDate()?->format(DateConstant::GLOBAL_FORMAT),
					$feedback->getDescriptionOfClaim(),
					$feedback->getStatus(),
				);
				$projectFeedbacks[] = $feedbackDto;
			}
		}

		$awaitingReview = false;

		$projectStatus = strtolower($project->getStatus());
		/** @var Task $task */
		foreach ($list as $task) {
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

			if (Project::STATUS_OPEN === $project->getStatus() && Task::STATUS_OPENED === $task->getStatus()) {
				if ($task->hasReviewActivity()) {
					$awaitingReview = true;
					$projectStatus = strtolower(Project::STATUS_REVIEW);
				}
			}

			if (Project::STATUS_COMPLAINT === $project->getStatus()) {
				if ($task->getFeedbacks()) {
					foreach ($task->getFeedbacks() as $feedback) {
						if (in_array($feedback->getFeedbackType(), [Feedback::TYPE_CLIENT_APPROVAL, Feedback::TYPE_INTERNAL_NONCONFORMITY])) {
							$projectStatus = strtolower(Project::STATUS_CLOSED);
							break;
						}
					}
				}
			}

			$taskDtoData = new TaskDto(
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
				status: $task->hasReviewActivity() ? Project::STATUS_REVIEW : strtolower($task->getStatus()),
				totalAgreed: UtilsService::amountFormat($task->getTotalAgreed()),
				tmSavings: UtilsService::amountFormat($task->getTmSavings()),
				workingFilesNumber: $task->getWorkingFilesNumber(),
				progress: ['total' => $task->getTotalActivities(), 'percentage' => number_format($percentage, 2)],
				awaitingReview: $task->hasReviewActivity() || $task->getTaskForReview()->count() > 0,
				forReview: [],
			);

			$tasks[] = $taskDtoData;
		}

		if (isset($list[0])) {
			/** @var Task $firstTask */
			$firstTask = $list[0];
			$projectInvoiceId = $firstTask?->getCustomerInvoice()?->getId();
			$projectInvoiceNumber = $firstTask?->getCustomerInvoice()?->getFinalNumber();
		}

		$projectDtoData = new ProjectDto(
			id: $project->getId(),
			idNumber: $project->getIdNumber(),
			refNumber: $project->getCustomerProjectNumber(),
			name: $project->getName(),
			totalAgreed: UtilsService::amountFormat($project->getTotalAgreed()),
			tmSavings: UtilsService::amountFormat($project->getTmSavings()),
			sourceLanguages: $sourceLanguages,
			targetLanguages: $targetLanguages,
			inputFiles: $inputFiles,
			additionalContacts: $additionalContacts,
			startDate: $startDate,
			deadline: $deadline,
			deliveryDate: $deliveryDate,
			closeDate: $closeDate,
			status: $projectStatus,
			customerSpecialInstructions: $project->getCustomerSpecialInstructions(),
			costCenter: $project->getCostCenter(),
			currency: $currencyDto,
			confirmationSentDate: $confirmationSentDate,
			service: $service,
			specialization: $specialization,
			rapidFire: $project->getRapidFire(),
			rush: $project->getRush(),
			projectManager: $projectManagerDto,
			requestedBy: $requestedByDto,
			feedbacks: ($projectFeedbacks) ?: $project->getFeedbacks()->getValues(),
			office: $project->getCustomer()?->getName(),
			progress: $projectProgress,
			awaitingReview: $awaitingReview,
			projectManagerProfilePic: $data['projectManagerPicData'],
			accountManagerProfilePic: $data['accountManagerPicData'],
			surveySent: $project->getSurveySent(),
			archived: !(null === $project->getArchivedAt()),
			quoteId: $project->getQuote()?->getId(),
			invoiceId: isset($projectInvoiceId) ? $projectInvoiceId : null,
			invoiceNumber: isset($projectInvoiceNumber) ? $projectInvoiceNumber : null,
			customFields: $customFields,
			customer: [
				'id' => $project->getCustomer()->getId(),
				'name' => $project->getCustomer()->getName(),
			],
		);

		return [
			'project' => $projectDtoData,
			'tasks' => $tasks,
		];
	}
}
