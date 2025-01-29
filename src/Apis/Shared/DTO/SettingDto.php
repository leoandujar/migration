<?php

namespace App\Apis\Shared\DTO;

class SettingDto
{
	public function __construct(
		public ?SettingProjectDto $projects,
		public ?SettingQuoteDto $quotes,
		public ?SettingInvoiceDto $invoices,
		public ?SettingReportDto $reports,
		public ?array $general,
	) {
	}
}
