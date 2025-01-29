<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'cp_setting_quote')]
#[ORM\Index(name: '', columns: ['cp_setting_quote_id'])]
#[ORM\Entity]
class CPSettingQuote implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'cp_setting_quote_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'cp_setting_quote_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'working_files_as_ref_files', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $workingFilesAsRefFiles;

	#[ORM\Column(name: 'update_working_files', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $updateWorkingFiles;

	#[ORM\Column(name: 'confirmation_send_by_default', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $confirmationSendByDefault;

	#[ORM\Column(name: 'download_confirmation', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $downloadConfirmation;

	#[ORM\Column(name: 'deadline_options', type: 'json', nullable: true)]
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

	#[ORM\OneToOne(targetEntity: CPSetting::class, mappedBy: 'quoteSettings', cascade: ['persist', 'remove'])]
	private CPSetting $settings;

	/**
	 * CPSettinguote constructor.
	 */
	public function __construct()
	{
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getWorkingFilesAsRefFiles(): ?bool
	{
		return $this->workingFilesAsRefFiles;
	}

	/**
	 * @return mixed
	 */
	public function setWorkingFilesAsRefFiles(bool $workingFilesAsRefFiles): self
	{
		$this->workingFilesAsRefFiles = $workingFilesAsRefFiles;

		return $this;
	}

	public function getUpdateWorkingFiles(): ?bool
	{
		return $this->updateWorkingFiles;
	}

	/**
	 * @return mixed
	 */
	public function setUpdateWorkingFiles(bool $updateWorkingFiles): self
	{
		$this->updateWorkingFiles = $updateWorkingFiles;

		return $this;
	}

	public function getConfirmationSendByDefault(): ?bool
	{
		return $this->confirmationSendByDefault;
	}

	/**
	 * @return mixed
	 */
	public function setConfirmationSendByDefault(bool $confirmationSendByDefault): self
	{
		$this->confirmationSendByDefault = $confirmationSendByDefault;

		return $this;
	}

	public function getDownloadConfirmation(): ?bool
	{
		return $this->downloadConfirmation;
	}

	/**
	 * @return mixed
	 */
	public function setDownloadConfirmation(bool $downloadConfirmation): self
	{
		$this->downloadConfirmation = $downloadConfirmation;

		return $this;
	}

	public function getDeadlineOptions(): ?array
	{
		return $this->deadlineOptions ?? [];
	}

	/**
	 * @return mixed
	 */
	public function setDeadlineOptions(?array $deadlineOptions): self
	{
		$this->deadlineOptions = $deadlineOptions;

		return $this;
	}

	public function getDuplicateTask(): ?bool
	{
		return $this->duplicateTask;
	}

	public function setDuplicateTask(bool $duplicateTask): self
	{
		$this->duplicateTask = $duplicateTask;

		return $this;
	}

	public function getAnalyzeFiles(): ?bool
	{
		return $this->analyzeFiles;
	}

	/**
	 * @return mixed
	 */
	public function setAnalyzeFiles(bool $analyzeFiles): self
	{
		$this->analyzeFiles = $analyzeFiles;

		return $this;
	}

	public function getQuickEstimate(): ?bool
	{
		return $this->quickEstimate;
	}

	/**
	 * @return mixed
	 */
	public function setQuickEstimate(bool $quickEstimate): self
	{
		$this->quickEstimate = $quickEstimate;

		return $this;
	}

	public function getCustomFields(): ?array
	{
		return $this->customFields;
	}

	/**
	 * @return mixed
	 */
	public function setCustomFields(?array $customFields): self
	{
		$this->customFields = $customFields;

		return $this;
	}

	public function getSettings(): ?CPSetting
	{
		return $this->settings;
	}

	/**
	 * @return mixed
	 */
	public function setSettings(?CPSetting $settings): self
	{
		$this->settings = $settings;

		// set (or unset) the owning side of the relation if necessary
		$newQuoteSettings = null === $settings ? null : $this;
		if ($settings->getQuoteSettings() !== $newQuoteSettings) {
			$settings->setQuoteSettings($newQuoteSettings);
		}

		return $this;
	}

	public function getDeadlinePrediction(): ?bool
	{
		return $this->deadlinePrediction;
	}

	/**
	 * @return mixed
	 */
	public function setDeadlinePrediction(bool $deadlinePrediction): self
	{
		$this->deadlinePrediction = $deadlinePrediction;

		return $this;
	}
}
