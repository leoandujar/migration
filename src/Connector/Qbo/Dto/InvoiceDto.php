<?php

namespace App\Connector\Qbo\Dto;

class InvoiceDto
{
	public function __construct(
		public ?string $customerRef,
		public array $lines,
		public ?float $totalAmount,
		public ?string $docNumber,
		public ?string $dueDate,
		public ?string $finalDate,
		public ?array $customFields,
	) {
	}

	public function toArray(): array
	{
		return [
			'DocNumber' => $this->docNumber,
			'Line' => $this->lines,
			'CustomerRef' => [
				'value' => $this->customerRef,
			],
			'DueDate' => $this->dueDate,
			'TxnDate' => $this->finalDate,
			'TotalAmt' => $this->totalAmount,
			'CustomField' => $this->customFields,
		];
	}
}
