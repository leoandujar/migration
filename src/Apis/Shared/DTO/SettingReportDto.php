<?php

namespace App\Apis\Shared\DTO;

class SettingReportDto
{
	public function __construct(
		public ?array $predefinedData
	) {
	}
}
