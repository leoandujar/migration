<?php

namespace App\Model\Entity;

use App\Linker\Services\RedisClients;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'cp_setting_project')]
#[ORM\Index(columns: ['cp_setting_project_id'], name: '')]
#[ORM\Entity]
class CPSettingProject implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'cp_setting_project_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'cp_setting_project_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'working_files_as_ref_files', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $workingFilesAsRefFiles;

	#[ORM\Column(name: 'update_working_files', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $updateWorkingFiles;

	#[ORM\Column(name: 'confirmation_send_by_default', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $confirmationSendByDefault;

	#[ORM\Column(name: 'download_confirmation', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $downloadConfirmation;

	#[ORM\Column(name: 'deadline_options', type: 'json', length: 100, nullable: true)]
	private ?array $deadlineOptions = [];

	#[ORM\Column(name: 'duplicate_task', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $duplicateTask;

	#[ORM\Column(name: 'analyze_files', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $analyzeFiles;

	#[ORM\Column(name: 'quick_estimate', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $quickEstimate;

	#[ORM\Column(name: 'deadline_prediction', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $deadlinePrediction;

	#[ORM\Column(name: 'custom_fields', type: 'json', nullable: true)]
	private ?array $customFields = [];

	#[ORM\Column(name: 'custom_fields_new', type: 'json', nullable: true)]
	private ?array $customFieldsNew = [];

	#[ORM\OneToOne(mappedBy: 'projectSettings', targetEntity: CPSetting::class, cascade: ['persist', 'remove'])]
	private ?CPSetting $settings;

	#[ORM\Column(name: 'autostart', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $autostart;

	#[ORM\Column(name: 'dearchive', type: 'boolean', nullable: true, options: ['default' => 'false'])]
	private ?bool $dearchive;

	#[ORM\Column(name: 'max_file_size', type: 'integer', nullable: false, options: ['default' => 500])]
	private int $maxFileSize;

	#[ORM\Column(name: 'files_queue', type: 'string', length: 60, nullable: false, options: ['default' => RedisClients::SESSION_KEY_PROJECTS_QUOTES_NORMAL])]
	private ?string $filesQueue;

	#[ORM\Column(name: 'categories', type: 'json', length: 100, nullable: true)]
	private ?array $categories = [];

	#[ORM\Column(name: 'rush_deadline', type: 'integer', nullable: true)]
	private ?int $rushDeadline;

	#[ORM\Column(name: 'file_extensions_warning', type: 'json', length: 100, nullable: true)]
	private ?array $fileExtensionsWarning = [];

	public function getId(): ?string
	{
		return $this->id;
	}

	public function isWorkingFilesAsRefFiles(): ?bool
	{
		return $this->workingFilesAsRefFiles;
	}

	public function setWorkingFilesAsRefFiles(bool $workingFilesAsRefFiles): self
	{
		$this->workingFilesAsRefFiles = $workingFilesAsRefFiles;

		return $this;
	}

	public function isUpdateWorkingFiles(): ?bool
	{
		return $this->updateWorkingFiles;
	}

	public function setUpdateWorkingFiles(bool $updateWorkingFiles): self
	{
		$this->updateWorkingFiles = $updateWorkingFiles;

		return $this;
	}

	public function isConfirmationSendByDefault(): ?bool
	{
		return $this->confirmationSendByDefault;
	}

	public function setConfirmationSendByDefault(bool $confirmationSendByDefault): self
	{
		$this->confirmationSendByDefault = $confirmationSendByDefault;

		return $this;
	}

	public function isDownloadConfirmation(): ?bool
	{
		return $this->downloadConfirmation;
	}

	public function setDownloadConfirmation(bool $downloadConfirmation): self
	{
		$this->downloadConfirmation = $downloadConfirmation;

		return $this;
	}

	public function getDeadlineOptions(): ?array
	{
		return $this->deadlineOptions ?? [];
	}

	public function setDeadlineOptions(?array $deadlineOptions): self
	{
		$this->deadlineOptions = $deadlineOptions;

		return $this;
	}

	public function isDuplicateTask(): ?bool
	{
		return $this->duplicateTask;
	}

	public function setDuplicateTask(bool $duplicateTask): self
	{
		$this->duplicateTask = $duplicateTask;

		return $this;
	}

	public function isAnalyzeFiles(): ?bool
	{
		return $this->analyzeFiles;
	}

	public function setAnalyzeFiles(bool $analyzeFiles): self
	{
		$this->analyzeFiles = $analyzeFiles;

		return $this;
	}

	public function isQuickEstimate(): ?bool
	{
		return $this->quickEstimate;
	}

	public function setQuickEstimate(bool $quickEstimate): self
	{
		$this->quickEstimate = $quickEstimate;

		return $this;
	}

	public function getCustomFields(): ?array
	{
		return $this->customFields;
	}

	public function setCustomFields(?array $customFields): self
	{
		$this->customFields = $customFields;

		return $this;
	}

	public function getSettings(): ?CPSetting
	{
		return $this->settings;
	}

	public function setSettings(?CPSetting $settings): self
	{
		$this->settings = $settings;

		// set (or unset) the owning side of the relation if necessary
		$newProjectSettings = null === $settings ? null : $this;
		if ($settings->getProjectSettings() !== $newProjectSettings) {
			$settings->setProjectSettings($newProjectSettings);
		}

		return $this;
	}

	public function isDeadlinePrediction(): ?bool
	{
		return $this->deadlinePrediction;
	}

	public function setDeadlinePrediction(bool $deadlinePrediction): self
	{
		$this->deadlinePrediction = $deadlinePrediction;

		return $this;
	}

	public function isAutostart(): ?bool
	{
		return $this->autostart;
	}

	public function setAutostart(bool $autostart): self
	{
		$this->autostart = $autostart;

		return $this;
	}

	public function getMaxFileSize(): ?int
	{
		return $this->maxFileSize;
	}

	public function setMaxFileSize(int $maxFileSize): self
	{
		$this->maxFileSize = $maxFileSize;

		return $this;
	}

	public function getFilesQueue(): ?string
	{
		return $this->filesQueue;
	}

	public function setFilesQueue(?string $filesQueue): static
	{
		$this->filesQueue = $filesQueue;

		return $this;
	}

	public function getCustomFieldsNew(): ?array
	{
		return $this->customFieldsNew;
	}

	public function setCustomFieldsNew(?array $customFieldsNew): static
	{
		$this->customFieldsNew = $customFieldsNew;

		return $this;
	}

	public function getCategories(): ?array
	{
		return $this->categories ?? [];
	}

	public function setCategories(?array $categories): self
	{
		$this->categories = $categories;

		return $this;
	}

	public function getRushDeadline(): ?int
	{
		return $this->rushDeadline;
	}

	public function setRushDeadline(?int $rushDeadline): static
	{
		$this->rushDeadline = $rushDeadline;

		return $this;
	}

	public function getFileExtensionsWarning(): ?array
	{
		return $this->fileExtensionsWarning;
	}

	public function setFileExtensionsWarning(?array $fileExtensionsWarning): static
	{
		$this->fileExtensionsWarning = $fileExtensionsWarning;

		return $this;
	}

	public function isDearchive(): ?bool
	{
		return $this->dearchive;
	}

	public function setDearchive(?bool $dearchive): static
	{
		$this->dearchive = $dearchive;

		return $this;
	}
}
