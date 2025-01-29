<?php

declare(strict_types=1);

namespace App\Apis\CustomerPortal\Http\Response\Quote;

use App\Apis\Shared\Util\UtilsService;
use App\Model\Entity\User;
use App\Model\Entity\Quote;
use App\Model\Entity\Currency;
use App\Apis\Shared\DTO\QuoteDto;
use App\Model\Entity\XtrfLanguage;
use App\Model\Entity\ContactPerson;
use App\Apis\Shared\DTO\CurrencyDto;
use App\Apis\Shared\DTO\LanguageDto;
use App\Apis\Shared\DTO\GenericPersonDto;
use App\Apis\Shared\DTO\TaskDto;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Constant\DateConstant;

class GetQuoteResponse extends ApiResponse
{
	/**
	 * GetQuoteResponse constructor.
	 */
	public function __construct(?array $data = null)
	{
		parent::__construct();
		$this->updateData($data);
	}

	public function marshall(mixed $data = null): array
	{
		/** @var Quote $quote */
		$quote = $data['quote'];
		$list = $data['tasks'];
		$inputFiles = $data['inputFiles'] ?? [];
		$tasks = [];

		$quoteDto = new QuoteDto();
		$languagesCombinations = $quote->getLanguagesCombinations();

		$startDate = $quote->getStartDate()?->format(DateConstant::GLOBAL_FORMAT);
		$deadline = $quote->getDeadline()?->format(DateConstant::GLOBAL_FORMAT);

		$service = $quote->getService()?->getName();
		$currencyDto = null;
		if ($quote->getCurrency() instanceof Currency) {
			$currencyDto = new CurrencyDto();
			$currencyDto
				->setName($quote->getCurrency()->getName())
				->setSymbol($quote->getCurrency()->getSymbol());
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

		$quoteDto
			->setId($quote->getId())
			->setIdNumber($quote->getIdNumber())
			->setRefNumber($quote->getCustomerProjectNumber())
			->setName($quote->getName())
			->setTotalAgreed(strval(UtilsService::amountFormat($quote->getTotalAgreed())))
			->setTmSavings(strval(UtilsService::amountFormat($quote->getTmSavings())))
			->setStartDate($startDate)
			->setDeadline($deadline)
			->setStatus(strtolower($quote->getStatus()))
			->setCustomerSpecialInstructions($quote->getCustomerSpecialInstructions())
			->setCurrency($currencyDto)
			->setService($service)
			->setInputFiles($inputFiles)
			->setAccountManagerProfilePic($data['accountManagerPicData'])
			->setProjectManagerProfilePic($data['projectManagerPicData'])
			->setProjectId($quote->getProject()?->getId())
			->setCustomFields($customFields)
			->setInstructions($quote->getCustomerSpecialInstructions())
			->setOffice($quote->getCustomer()?->getName())
			->setSpecialization($quote->getSpecialization()?->getName());

		foreach ($quote->getCustomersPerson() as $contactPerson) {
			$quoteDto->additionalContacts[] = [
				'id' => $contactPerson->getContactPerson()->getId(),
				'name' => $contactPerson->getContactPerson()->getName(),
				'lastName' => $contactPerson->getContactPerson()->getLastName(),
			];
		}

		if (null !== $quote->getProjectManager() && $quote->getProjectManager() instanceof User) {
			/** @var User $manager */
			$manager = $quote->getProjectManager();
			$quoteManagerDto = new GenericPersonDto(
				$manager->getId(),
				$manager->getFirstName(),
				$manager->getLastName(),
				$manager->getEmail(),
			);
			$quoteDto->setProjectManager($quoteManagerDto);
		}

		if (null !== $quote->getCustomerContactPerson()?->getContactPerson() && $quote->getCustomerContactPerson()->getContactPerson() instanceof ContactPerson) {
			/** @var ContactPerson $requestedBy */
			$requestedBy = $quote->getCustomerContactPerson()->getContactPerson();
			$requestedByDto = new GenericPersonDto(
				$requestedBy->getId(),
				$requestedBy->getName(),
				$requestedBy->getLastName(),
				$requestedBy->getEmail(),
			);
			$quoteDto->setRequestedBy($requestedByDto);
		}
		$sourceLanguageIds = [];
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
					$quoteDto->sourceLanguages[] = $langSourceDto;
				}
			}
			if (null !== $languagesCombination->getTargetLanguage() && $languagesCombination->getTargetLanguage() instanceof XtrfLanguage) {
				$langtargetDto = new LanguageDto();
				$langtargetDto
					->setId($languagesCombination->getTargetLanguage()->getId())
					->setName($languagesCombination->getTargetLanguage()->getName())
					->setSymbol($languagesCombination->getTargetLanguage()->getSymbol());
				$quoteDto->targetLanguages[] = $langtargetDto;
			}
		}

		$quoteAwaitingReview = false;
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

			if ($task->getTaskForReview()->count() > 0) {
				$quoteAwaitingReview = true;
			}

			$percentage = $task->getTotalActivities() > 0 ? ($task->getProgressActivities() * 100) / $task->getTotalActivities() : 0;

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
				status: strtolower($task->getStatus()),
				totalAgreed: UtilsService::amountFormat($task->getTotalAgreed()),
				tmSavings: UtilsService::amountFormat($task->getTmSavings()),
				workingFilesNumber: $task->getWorkingFilesNumber(),
				progress: ['total' => $task->getTotalActivities(), 'percentage' => number_format($percentage, 2)],
				awaitingReview: $task->getTaskForReview()->count() > 0,
				forReview: $task->forReview ?? null,
			);

			$tasks[] = $taskDtoData;
		}
		$quoteDto->setAwaitingReview($quoteAwaitingReview);

		return [
			'quote' => $quoteDto,
			'tasks' => $tasks,
		];
	}
}
