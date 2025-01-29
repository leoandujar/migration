<?php

namespace App\Apis\Shared\DTO;

class ProjectDto
{
	public function __construct(
		public string $id,
		public string $idNumber,
		public ?string $refNumber = null,
		public ?string $name = null,
		public ?string $totalAgreed = null,
		public ?string $tmSavings = null,
		public LanguageDto|array $sourceLanguages = [],
		public LanguageDto|array $targetLanguages = [],
		public array $inputFiles = [],
		public array $additionalContacts = [],
		public ?string $startDate = null,
		public ?string $deadline = null,
		public ?string $deliveryDate = null,
		public ?string $closeDate = null,
		public ?string $status = null,
		public ?string $customerSpecialInstructions = null,
		public ?string $costCenter = null,
		public ?CurrencyDto $currency = null,
		public ?string $confirmationSentDate = null,
		public ?string $service = null,
		public ?string $specialization = null,
		public ?string $rapidFire = null,
		public ?bool $rush = null,
		public ?GenericPersonDto $projectManager = null,
		public ?GenericPersonDto $requestedBy = null,
		public FeedbackDTO|array $feedbacks = [],
		public ?string $office = null,
		public ?array $progress = [
			'total' => 0,
			'percentage' => 0,
		],
		public ?bool $awaitingReview = null,
		public ?string $projectManagerProfilePic = null,
		public ?string $accountManagerProfilePic = null,
		public ?bool $surveySent = null,
		public ?bool $archived = null,
		public ?string $quoteId = null,
		public ?string $invoiceId = null,
		public ?string $invoiceNumber = null,
		public ?array $tasksForReview = [],
		public ?array $customFields = [],
		public ?array $customer = [
			'id' => null,
			'name' => null,
		],
	) {
	}
}
