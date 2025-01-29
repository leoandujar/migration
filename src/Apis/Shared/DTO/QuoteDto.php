<?php

namespace App\Apis\Shared\DTO;

class QuoteDto
{
	public function __construct(
		public string $id,
		public string $idNumber,
		public ?string $refNumber,
		public ?string $name,
		public ?string $totalAgreed,
		public ?string $tmSavings,
		public LanguageDto|array $sourceLanguages = [],
		public LanguageDto|array $targetLanguages = [],
		public array $inputFiles = [],
		public ?array $customFields = [],
		public array $additionalContacts = [],
		public string $startDate,
		public ?string $deadline,
		public string $status,
		public ?string $customerSpecialInstructions,
		public CurrencyDto $currency,
		public ?string $service,
		public ?GenericPersonDto $projectManager,
		public ?GenericPersonDto $requestedBy,
		public ?bool $awaitingReview,
		public ?string $projectManagerProfilePic,
		public ?string $accountManagerProfilePic,
		public ?string $projectId,
		public ?string $instructions,
		public ?string $office,
		public ?string $specialization,
	) {
	}

	public function getOffice(?string $office): self
	{
		$this->office = $office;

		return $this;
	}
}
