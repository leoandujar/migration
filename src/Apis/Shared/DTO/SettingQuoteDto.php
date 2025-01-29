<?php

namespace App\Apis\Shared\DTO;

class SettingQuoteDto
{
	public function __construct(
		public ?bool $workingFilesAsRefFiles,
		public ?bool $updateWorkingFiles,
		public ?bool $confirmationSendByDefault,
		public ?bool $downloadConfirmation,
		public ?array $deadlineOptions,
		public ?bool $analyzeFiles,
		public ?bool $duplicateTask,
		public ?bool $quickEstimate,
		public ?bool $deadlinePrediction,
		public ?array $customFields
	) {
	}
}
