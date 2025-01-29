<?php

namespace App\Apis\Shared\DTO;

class FeatureProjectDto
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
		public ?bool $autoStart,
		public ?int $maxFileSize,
		public ?string $filesQueue,
		public ?int $rushDeadline,
		public ?array $categories,
		public ?array $fileExtensionsWarning,
		public ?bool $dearchive,
	) {
	}
}
