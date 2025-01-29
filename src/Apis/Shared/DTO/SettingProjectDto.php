<?php

namespace App\Apis\Shared\DTO;

class SettingProjectDto
{
	public function __construct(
		public ?FeatureProjectDto $features,
		public ?array $customFields,
	) {
	}
}
