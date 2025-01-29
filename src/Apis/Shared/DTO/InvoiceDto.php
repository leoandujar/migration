<?php

namespace App\Apis\Shared\DTO;

class InvoiceDto
{
	public function __construct(
		public string $id,
		public ?string $idNumber,
		public string $status,
		public ?string $fullyPaidDate,
		public ?string $invoiceNote,
		public string $paidValue,
		public string $internalStatus,
		public ?string $dueDate,
		public ?string $finalDate,
		public string $totalNetto,
		public ?string $dueAmount,
		public ?string $customer,
		public ?CurrencyDto $currency,
		public ?string $qboId,
		public ?array $tasks = [],
		public ?string $projectId,
		public ?string $projectName,
		public ?string $projectNumber
	) {
	}
}
