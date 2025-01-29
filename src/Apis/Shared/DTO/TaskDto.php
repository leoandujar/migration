<?php

namespace App\Apis\Shared\DTO;

class TaskDto
{
	public function __construct(
		public string $id,
		public string $activitiesStatus,
		public ?string $actualStartDate,
		public ?string $closeDate,
		public ?bool $confirmedFilesDownloading,
		public ?string $customerInvoiceId,
		public ?string $customerInvoiceNumber,
		public ?string $deadline,
		public ?string $deliveryDate,
		public ?string $estimatedDeliveryDate,
		public ?string $finalInvoiceDate,
		public ?bool $invoiceable,
		public ?string $ontimeStatus,
		public ?string $partialDeliveryDate,
		public ?string $projectPhaseIdNumber,
		public ?LanguageDto $sourceLanguage,
		public ?LanguageDto $targetLanguage,
		public string $status,
		public ?string $totalAgreed,
		public ?string $tmSavings,
		public ?int $workingFilesNumber,
		public array $progress = [
			'total' => 0,
			'percentage' => 0,
		],
		public ?bool $awaitingReview = false,
		public ?array $forReview = []
	) {
	}
}
