<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'activity')]
#[ORM\UniqueConstraint(name: 'activity_project_phase_id_number_key', columns: ['project_phase_id_number'])]
#[ORM\UniqueConstraint(name: 'activity_quote_phase_id_number_key', columns: ['quote_phase_id_number'])]
#[ORM\Entity]
class Activity implements EntityInterface
{
	public const STATUS_READY = 'READY';
	public const STATUS_STARTED = 'STARTED';
	public const STATUS_ACCEPTED = 'ACCEPTED';
	public const STATUS_OPENED = 'OPENED';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'activity_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'activity_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'amount_modifiers', type: 'string', nullable: true)]
	private ?string $amountModifiers;

	#[ORM\Column(name: 'assign_first_provider', type: 'boolean', nullable: true)]
	private ?bool $assignFirstProvider;

	#[ORM\Column(name: 'auto_split_activity_if_needed', type: 'boolean', nullable: true)]
	private ?bool $autoSplitActivityIfNeeded;

	#[ORM\Column(name: 'auto_total_agreed', type: 'boolean', nullable: true)]
	private ?bool $autoTotalAgreed;

	#[ORM\Column(name: 'customer_special_instructions', type: 'text', nullable: true)]
	private ?string $customerSpecialInstructions;

	#[ORM\Column(name: 'exchange_ratio_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $exchangeRatioDate;

	#[ORM\Column(name: 'ignore_minimal_charge', type: 'boolean', nullable: true)]
	private ?bool $ignoreMinimalCharge;

	#[ORM\Column(name: 'internal_special_instructions', type: 'text', nullable: true)]
	private ?string $internalSpecialInstructions;

	#[ORM\Column(name: 'manual_amount_modifier_name', type: 'text', nullable: true)]
	private ?string $manualAmountModifierName;

	#[ORM\Column(name: 'minimal_charge', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $minimalCharge;

	#[ORM\Column(name: 'payment_note', type: 'text', nullable: true)]
	private ?string $paymentNote;

	#[ORM\Column(name: 'provider_special_instructions', type: 'text', nullable: true)]
	private ?string $providerSpecialInstructions;

	#[ORM\Column(name: 'requests_deadline', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $requestsDeadline;

	#[ORM\Column(name: 'total_agreed', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $totalAgreed;

	#[ORM\Column(name: 'total_amount_modifier', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $totalAmountModifier;

	#[ORM\Column(name: 'words', type: 'integer', nullable: true)]
	private ?int $words;

	#[ORM\Column(name: 'auto_calculate_payment_dates', type: 'boolean', nullable: true)]
	private ?bool $autoCalculatePaymentDates;

	#[ORM\Column(name: 'bundles_for_output', type: 'string', nullable: false)]
	private string $bundlesForOutput;

	#[ORM\Column(name: 'actual_start_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $actualStartDate;

	#[ORM\Column(name: 'close_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $closeDate;

	#[ORM\Column(name: 'deadline', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $deadline;

	#[ORM\Column(name: 'start_date', type: 'datetime', nullable: false)]
	private \DateTimeInterface $startDate;

	#[ORM\Column(name: 'directory', type: 'string', length: 2000, nullable: true)]
	private ?string $directory;

	#[ORM\Column(name: 'draft_invoice_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $draftInvoiceDate;

	#[ORM\Column(name: 'rating_note', type: 'float', nullable: false)]
	private float $ratingNote;

	#[ORM\Column(name: 'files_download_confirmed', type: 'boolean', nullable: true)]
	private ?bool $filesDownloadConfirmed;

	#[ORM\Column(name: 'files_support', type: 'boolean', nullable: false)]
	private bool $filesSupport;

	#[ORM\Column(name: 'final_invoice_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $finalInvoiceDate;

	#[ORM\Column(name: 'input_files_mode', type: 'string', nullable: true)]
	private ?string $inputFilesMode;

	#[ORM\Column(name: 'internal_feedback', type: 'text', nullable: true)]
	private ?string $internalFeedback;

	#[ORM\Column(name: 'invoice_activity_position', type: 'integer', nullable: true)]
	private ?int $invoiceActivityPosition;

	#[ORM\Column(name: 'invoiceable', type: 'boolean', nullable: true)]
	private ?bool $invoiceable;

	#[ORM\Column(name: 'invoiced_in_quote_phase', type: 'boolean', nullable: false)]
	private bool $invoiceableInQuotePhase;

	#[ORM\Column(name: 'notes_from_provider', type: 'text', nullable: true)]
	private ?string $notesFromProvider;

	#[ORM\Column(name: 'order_status', type: 'string', nullable: false)]
	private string $oderStatus;

	#[ORM\Column(name: 'payment_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $paymentDate;

	#[ORM\Column(name: 'project_order_recipient_person_type', type: 'string', nullable: true)]
	private ?string $projectOrderRecipientPersonType;

	#[ORM\Column(name: 'project_phase_id_number', type: 'string', nullable: true)]
	private ?string $projectPhaseIdNumber;

	#[ORM\Column(name: 'quote_phase_id_number', type: 'string', nullable: true)]
	private ?string $quotePhaseIdNumber;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\ManyToOne(targetEntity: ActivityType::class)]
	#[ORM\JoinColumn(name: 'activity_type_id', referencedColumnName: 'activity_type_id', nullable: false)]
	private ActivityType $activityType;

	#[ORM\ManyToOne(targetEntity: ProviderPerson::class)]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?ProviderPerson $contactPerson;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\Column(name: 'provider_price_profile_id', type: 'bigint', nullable: true)]
	private ?string $providerPriceProfileId;

	#[ORM\ManyToOne(targetEntity: WorkflowJobInstance::class)]
	#[ORM\JoinColumn(name: 'workflow_job_instance_id', referencedColumnName: 'workflow_job_instance_id', nullable: true)]
	private ?WorkflowJobInstance $workflowJobInstce;

	#[ORM\Column(name: 'meta_directory_id', type: 'bigint', nullable: true)]
	private ?string $metaDirectoryId;

	#[ORM\Column(name: 'deadline_reminder_id', type: 'bigint', nullable: true)]
	private ?string $deadlineReminderId;

	#[ORM\ManyToOne(targetEntity: ProviderInvoice::class)]
	#[ORM\JoinColumn(name: 'provider_invoice_id', referencedColumnName: 'provider_invoice_id', nullable: true)]
	private ?ProviderInvoice $providerInvoice;

	#[ORM\ManyToOne(targetEntity: PaymentCondition::class)]
	#[ORM\JoinColumn(name: 'payment_conditions_id', referencedColumnName: 'payment_conditions_id', nullable: true)]
	private ?PaymentCondition $paymentConditions;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'activities')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', nullable: false)]
	private Task $task;

	#[ORM\Column(name: 'template_id', type: 'bigint', nullable: true)]
	private ?string $templateId;

	#[ORM\ManyToOne(targetEntity: VatRate::class)]
	#[ORM\JoinColumn(name: 'vat_rate_id', referencedColumnName: 'vat_rate_id', nullable: false)]
	private VatRate $vatRate;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\Column(name: 'generate_links_for_external_system', type: 'boolean', nullable: true, options: ['default' => 'true'])]
	private ?bool $generateLinksForExternalSystem;

	#[ORM\Column(name: 'partially_finished', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $partiallyFinished;

	#[ORM\Column(name: 'provider_selection_settings_id', type: 'bigint', nullable: true)]
	private ?string $providerSelectionSettingsId;

	#[ORM\Column(name: 'auction_active', type: 'boolean', nullable: false)]
	private bool $auctionActive;

	#[ORM\Column(name: 'job_assignment_id', type: 'string', nullable: true)]
	private ?string $jobAssignmentId;

	#[ORM\Column(name: 'half_of_time_reminder_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $halfOfTimeReminderDate;

	#[ORM\Column(name: 'most_of_time_reminder_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $mostOfTimeReminderDate;

	#[ORM\Column(name: 'all_of_time_reminder_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $allOfTimeReminderDate;

	#[ORM\Column(name: 'job_invoicing_option', type: 'string', nullable: false)]
	private string $jobInvoicingOption;

	#[ORM\Column(name: 'notes_from_vendor_to_others', type: 'text', nullable: true)]
	private ?string $notesFromVendorToOthers;

	#[ORM\Column(name: 'visible_in_vp', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $visibleInVp;

	#[ORM\Column(name: 'assigned_person_id', type: 'bigint', nullable: true)]
	private ?string $assignedPersonId;

	#[ORM\Column(name: 'backup_old_exchange_ratio_not_used', type: 'decimal', precision: 19, scale: 10, nullable: true)]
	private ?float $backupOldExchangeRatioNotUsed;

	#[ORM\Column(name: 'exchange_ratio_event', type: 'string', nullable: true)]
	private ?string $exchangeRatioEvent;

	#[ORM\Column(name: 'purchase_order_sent_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $purchaseOrderSentDate;

	#[ORM\Column(name: 'auto_correct_file_policy', type: 'boolean', nullable: true, options: ['default' => 'true'])]
	private ?bool $autoCorrectFilePolicy;

	#[ORM\ManyToOne(targetEntity: Provider::class)]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: true)]
	private ?Provider $provider;

	#[ORM\Column(name: 'quality_analysis_score', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $qualityAnalysisScore;

	#[ORM\Column(name: 'provider_evaluation_responsiveness', type: 'text', nullable: true)]
	private ?string $providerEvaluationResponsiveness;

	#[ORM\Column(name: 'provider_evaluation_on_time_delivery', type: 'text', nullable: true)]
	private ?string $providerEvaluationOnTimeDelivery;

	#[ORM\Column(name: 'provider_evaluation_collaboration', type: 'text', nullable: true)]
	private ?string $providerEvaluationCollaboration;

	#[ORM\Column(name: 'provider_evaluation_instructions', type: 'text', nullable: true)]
	private ?string $providerEvaluationInstructions;

	#[ORM\Column(name: 'activity_name', type: 'string', length: 255, options: ['default' => ''])]
	private string $activityName;

	#[ORM\OneToMany(targetEntity: AnalyticsProject::class, mappedBy: 'job', cascade: ['detach'])]
	private mixed $analyticsProjects;

	#[ORM\OneToMany(targetEntity: ActivityAmountModifier::class, mappedBy: 'activity', orphanRemoval: true)]
	private mixed $amountModifiersList;

	#[ORM\JoinTable(name: 'previous_activities')]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'previous_activity_id', referencedColumnName: 'activity_id')]
	#[ORM\ManyToMany(targetEntity: Activity::class, cascade: ['persist'], inversedBy: 'previousActivities')]
	protected mixed $activities;

	#[ORM\ManyToMany(targetEntity: Activity::class, mappedBy: 'activities', cascade: ['persist'])]
	private mixed $previousActivities;

	#[ORM\OneToMany(targetEntity: ActivityCatCharge::class, mappedBy: 'activity', orphanRemoval: true)]
	private mixed $activityCatCharge;

	#[ORM\OneToMany(targetEntity: ActivityCharge::class, mappedBy: 'activity', orphanRemoval: true)]
	private mixed $activityCharge;

	public function __construct()
	{
		$this->analyticsProjects   = new ArrayCollection();
		$this->amountModifiersList = new ArrayCollection();
		$this->activities          = new ArrayCollection();
		$this->previousActivities  = new ArrayCollection();
		$this->activityCatCharge   = new ArrayCollection();
		$this->activityCharge      = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	/**
	 * @return mixed
	 */
	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getAmountModifiers(): ?string
	{
		return $this->amountModifiers;
	}

	/**
	 * @return mixed
	 */
	public function setAmountModifiers(?string $amountModifiers): self
	{
		$this->amountModifiers = $amountModifiers;

		return $this;
	}

	public function getAssignFirstProvider(): ?bool
	{
		return $this->assignFirstProvider;
	}

	/**
	 * @return mixed
	 */
	public function setAssignFirstProvider(?bool $assignFirstProvider): self
	{
		$this->assignFirstProvider = $assignFirstProvider;

		return $this;
	}

	public function getAutoSplitActivityIfNeeded(): ?bool
	{
		return $this->autoSplitActivityIfNeeded;
	}

	/**
	 * @return mixed
	 */
	public function setAutoSplitActivityIfNeeded(?bool $autoSplitActivityIfNeeded): self
	{
		$this->autoSplitActivityIfNeeded = $autoSplitActivityIfNeeded;

		return $this;
	}

	public function getAutoTotalAgreed(): ?bool
	{
		return $this->autoTotalAgreed;
	}

	/**
	 * @return mixed
	 */
	public function setAutoTotalAgreed(?bool $autoTotalAgreed): self
	{
		$this->autoTotalAgreed = $autoTotalAgreed;

		return $this;
	}

	public function getCustomerSpecialInstructions(): ?string
	{
		return $this->customerSpecialInstructions;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerSpecialInstructions(?string $customerSpecialInstructions): self
	{
		$this->customerSpecialInstructions = $customerSpecialInstructions;

		return $this;
	}

	public function getExchangeRatioDate(): ?\DateTimeInterface
	{
		return $this->exchangeRatioDate;
	}

	/**
	 * @return mixed
	 */
	public function setExchangeRatioDate(?\DateTimeInterface $exchangeRatioDate): self
	{
		$this->exchangeRatioDate = $exchangeRatioDate;

		return $this;
	}

	public function getIgnoreMinimalCharge(): ?bool
	{
		return $this->ignoreMinimalCharge;
	}

	/**
	 * @return mixed
	 */
	public function setIgnoreMinimalCharge(?bool $ignoreMinimalCharge): self
	{
		$this->ignoreMinimalCharge = $ignoreMinimalCharge;

		return $this;
	}

	public function getInternalSpecialInstructions(): ?string
	{
		return $this->internalSpecialInstructions;
	}

	/**
	 * @return mixed
	 */
	public function setInternalSpecialInstructions(?string $internalSpecialInstructions): self
	{
		$this->internalSpecialInstructions = $internalSpecialInstructions;

		return $this;
	}

	public function getManualAmountModifierName(): ?string
	{
		return $this->manualAmountModifierName;
	}

	/**
	 * @return mixed
	 */
	public function setManualAmountModifierName(?string $manualAmountModifierName): self
	{
		$this->manualAmountModifierName = $manualAmountModifierName;

		return $this;
	}

	public function getMinimalCharge(): ?string
	{
		return (string)$this->minimalCharge;
	}

	/**
	 * @return mixed
	 */
	public function setMinimalCharge(?string $minimalCharge): self
	{
		$this->minimalCharge = $minimalCharge;

		return $this;
	}

	public function getPaymentNote(): ?string
	{
		return $this->paymentNote;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentNote(?string $paymentNote): self
	{
		$this->paymentNote = $paymentNote;

		return $this;
	}

	public function getProviderSpecialInstructions(): ?string
	{
		return $this->providerSpecialInstructions;
	}

	/**
	 * @return mixed
	 */
	public function setProviderSpecialInstructions(?string $providerSpecialInstructions): self
	{
		$this->providerSpecialInstructions = $providerSpecialInstructions;

		return $this;
	}

	public function getRequestsDeadline(): ?\DateTimeInterface
	{
		return $this->requestsDeadline;
	}

	/**
	 * @return mixed
	 */
	public function setRequestsDeadline(?\DateTimeInterface $requestsDeadline): self
	{
		$this->requestsDeadline = $requestsDeadline;

		return $this;
	}

	public function getTotalAgreed(): ?string
	{
		return (string)$this->totalAgreed;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAgreed(?string $totalAgreed): self
	{
		$this->totalAgreed = $totalAgreed;

		return $this;
	}

	public function getTotalAmountModifier(): ?string
	{
        return (string)$this->totalAmountModifier;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAmountModifier(?string $totalAmountModifier): self
	{
		$this->totalAmountModifier = $totalAmountModifier;

		return $this;
	}

	public function getWords(): ?int
	{
		return $this->words;
	}

	/**
	 * @return mixed
	 */
	public function setWords(?int $words): self
	{
		$this->words = $words;

		return $this;
	}

	public function getAutoCalculatePaymentDates(): ?bool
	{
		return $this->autoCalculatePaymentDates;
	}

	/**
	 * @return mixed
	 */
	public function setAutoCalculatePaymentDates(?bool $autoCalculatePaymentDates): self
	{
		$this->autoCalculatePaymentDates = $autoCalculatePaymentDates;

		return $this;
	}

	public function getBundlesForOutput(): ?string
	{
		return $this->bundlesForOutput;
	}

	/**
	 * @return mixed
	 */
	public function setBundlesForOutput(string $bundlesForOutput): self
	{
		$this->bundlesForOutput = $bundlesForOutput;

		return $this;
	}

	public function getActualStartDate(): ?\DateTimeInterface
	{
		return $this->actualStartDate;
	}

	/**
	 * @return mixed
	 */
	public function setActualStartDate(?\DateTimeInterface $actualStartDate): self
	{
		$this->actualStartDate = $actualStartDate;

		return $this;
	}

	public function getCloseDate(): ?\DateTimeInterface
	{
		return $this->closeDate;
	}

	/**
	 * @return mixed
	 */
	public function setCloseDate(?\DateTimeInterface $closeDate): self
	{
		$this->closeDate = $closeDate;

		return $this;
	}

	public function getDeadline(): ?\DateTimeInterface
	{
		return $this->deadline;
	}

	/**
	 * @return mixed
	 */
	public function setDeadline(?\DateTimeInterface $deadline): self
	{
		$this->deadline = $deadline;

		return $this;
	}

	public function getStartDate(): ?\DateTimeInterface
	{
		return $this->startDate;
	}

	/**
	 * @return mixed
	 */
	public function setStartDate(\DateTimeInterface $startDate): self
	{
		$this->startDate = $startDate;

		return $this;
	}

	public function getDirectory(): ?string
	{
		return $this->directory;
	}

	/**
	 * @return mixed
	 */
	public function setDirectory(?string $directory): self
	{
		$this->directory = $directory;

		return $this;
	}

	public function getDraftInvoiceDate(): ?\DateTimeInterface
	{
		return $this->draftInvoiceDate;
	}

	/**
	 * @return mixed
	 */
	public function setDraftInvoiceDate(?\DateTimeInterface $draftInvoiceDate): self
	{
		$this->draftInvoiceDate = $draftInvoiceDate;

		return $this;
	}

	public function getRatingNote(): ?float
	{
		return $this->ratingNote;
	}

	/**
	 * @return mixed
	 */
	public function setRatingNote(float $ratingNote): self
	{
		$this->ratingNote = $ratingNote;

		return $this;
	}

	public function getFilesDownloadConfirmed(): ?bool
	{
		return $this->filesDownloadConfirmed;
	}

	/**
	 * @return mixed
	 */
	public function setFilesDownloadConfirmed(?bool $filesDownloadConfirmed): self
	{
		$this->filesDownloadConfirmed = $filesDownloadConfirmed;

		return $this;
	}

	public function getFilesSupport(): ?bool
	{
		return $this->filesSupport;
	}

	/**
	 * @return mixed
	 */
	public function setFilesSupport(bool $filesSupport): self
	{
		$this->filesSupport = $filesSupport;

		return $this;
	}

	public function getFinalInvoiceDate(): ?\DateTimeInterface
	{
		return $this->finalInvoiceDate;
	}

	/**
	 * @return mixed
	 */
	public function setFinalInvoiceDate(?\DateTimeInterface $finalInvoiceDate): self
	{
		$this->finalInvoiceDate = $finalInvoiceDate;

		return $this;
	}

	public function getInputFilesMode(): ?string
	{
		return $this->inputFilesMode;
	}

	/**
	 * @return mixed
	 */
	public function setInputFilesMode(?string $inputFilesMode): self
	{
		$this->inputFilesMode = $inputFilesMode;

		return $this;
	}

	public function getInternalFeedback(): ?string
	{
		return $this->internalFeedback;
	}

	/**
	 * @return mixed
	 */
	public function setInternalFeedback(?string $internalFeedback): self
	{
		$this->internalFeedback = $internalFeedback;

		return $this;
	}

	public function getInvoiceActivityPosition(): ?int
	{
		return $this->invoiceActivityPosition;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceActivityPosition(?int $invoiceActivityPosition): self
	{
		$this->invoiceActivityPosition = $invoiceActivityPosition;

		return $this;
	}

	public function getInvoiceable(): ?bool
	{
		return $this->invoiceable;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceable(?bool $invoiceable): self
	{
		$this->invoiceable = $invoiceable;

		return $this;
	}

	public function getInvoiceableInQuotePhase(): ?bool
	{
		return $this->invoiceableInQuotePhase;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceableInQuotePhase(bool $invoiceableInQuotePhase): self
	{
		$this->invoiceableInQuotePhase = $invoiceableInQuotePhase;

		return $this;
	}

	public function getNotesFromProvider(): ?string
	{
		return $this->notesFromProvider;
	}

	/**
	 * @return mixed
	 */
	public function setNotesFromProvider(?string $notesFromProvider): self
	{
		$this->notesFromProvider = $notesFromProvider;

		return $this;
	}

	public function getOderStatus(): ?string
	{
		return $this->oderStatus;
	}

	/**
	 * @return mixed
	 */
	public function setOderStatus(string $oderStatus): self
	{
		$this->oderStatus = $oderStatus;

		return $this;
	}

	public function getPaymentDate(): ?\DateTimeInterface
	{
		return $this->paymentDate;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentDate(?\DateTimeInterface $paymentDate): self
	{
		$this->paymentDate = $paymentDate;

		return $this;
	}

	public function getProjectOrderRecipientPersonType(): ?string
	{
		return $this->projectOrderRecipientPersonType;
	}

	/**
	 * @return mixed
	 */
	public function setProjectOrderRecipientPersonType(?string $projectOrderRecipientPersonType): self
	{
		$this->projectOrderRecipientPersonType = $projectOrderRecipientPersonType;

		return $this;
	}

	public function getProjectPhaseIdNumber(): ?string
	{
		return $this->projectPhaseIdNumber;
	}

	/**
	 * @return mixed
	 */
	public function setProjectPhaseIdNumber(?string $projectPhaseIdNumber): self
	{
		$this->projectPhaseIdNumber = $projectPhaseIdNumber;

		return $this;
	}

	public function getQuotePhaseIdNumber(): ?string
	{
		return $this->quotePhaseIdNumber;
	}

	/**
	 * @return mixed
	 */
	public function setQuotePhaseIdNumber(?string $quotePhaseIdNumber): self
	{
		$this->quotePhaseIdNumber = $quotePhaseIdNumber;

		return $this;
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	/**
	 * @return mixed
	 */
	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getProviderPriceProfileId(): ?string
	{
		return $this->providerPriceProfileId;
	}

	/**
	 * @return mixed
	 */
	public function setProviderPriceProfileId(?string $providerPriceProfileId): self
	{
		$this->providerPriceProfileId = $providerPriceProfileId;

		return $this;
	}

	public function getMetaDirectoryId(): ?string
	{
		return $this->metaDirectoryId;
	}

	/**
	 * @return mixed
	 */
	public function setMetaDirectoryId(?string $metaDirectoryId): self
	{
		$this->metaDirectoryId = $metaDirectoryId;

		return $this;
	}

	public function getDeadlineReminderId(): ?string
	{
		return $this->deadlineReminderId;
	}

	/**
	 * @return mixed
	 */
	public function setDeadlineReminderId(?string $deadlineReminderId): self
	{
		$this->deadlineReminderId = $deadlineReminderId;

		return $this;
	}

	public function getTemplateId(): ?string
	{
		return $this->templateId;
	}

	/**
	 * @return mixed
	 */
	public function setTemplateId(?string $templateId): self
	{
		$this->templateId = $templateId;

		return $this;
	}

	public function getGenerateLinksForExternalSystem(): ?bool
	{
		return $this->generateLinksForExternalSystem;
	}

	/**
	 * @return mixed
	 */
	public function setGenerateLinksForExternalSystem(?bool $generateLinksForExternalSystem): self
	{
		$this->generateLinksForExternalSystem = $generateLinksForExternalSystem;

		return $this;
	}

	public function getPartiallyFinished(): ?bool
	{
		return $this->partiallyFinished;
	}

	/**
	 * @return mixed
	 */
	public function setPartiallyFinished(bool $partiallyFinished): self
	{
		$this->partiallyFinished = $partiallyFinished;

		return $this;
	}

	public function getProviderSelectionSettingsId(): ?string
	{
		return $this->providerSelectionSettingsId;
	}

	/**
	 * @return mixed
	 */
	public function setProviderSelectionSettingsId(?string $providerSelectionSettingsId): self
	{
		$this->providerSelectionSettingsId = $providerSelectionSettingsId;

		return $this;
	}

	public function getAuctionActive(): ?bool
	{
		return $this->auctionActive;
	}

	/**
	 * @return mixed
	 */
	public function setAuctionActive(bool $auctionActive): self
	{
		$this->auctionActive = $auctionActive;

		return $this;
	}

	public function getJobAssignmentId(): ?string
	{
		return $this->jobAssignmentId;
	}

	/**
	 * @return mixed
	 */
	public function setJobAssignmentId(?string $jobAssignmentId): self
	{
		$this->jobAssignmentId = $jobAssignmentId;

		return $this;
	}

	public function getHalfOfTimeReminderDate(): ?\DateTimeInterface
	{
		return $this->halfOfTimeReminderDate;
	}

	/**
	 * @return mixed
	 */
	public function setHalfOfTimeReminderDate(?\DateTimeInterface $halfOfTimeReminderDate): self
	{
		$this->halfOfTimeReminderDate = $halfOfTimeReminderDate;

		return $this;
	}

	public function getMostOfTimeReminderDate(): ?\DateTimeInterface
	{
		return $this->mostOfTimeReminderDate;
	}

	/**
	 * @return mixed
	 */
	public function setMostOfTimeReminderDate(?\DateTimeInterface $mostOfTimeReminderDate): self
	{
		$this->mostOfTimeReminderDate = $mostOfTimeReminderDate;

		return $this;
	}

	public function getAllOfTimeReminderDate(): ?\DateTimeInterface
	{
		return $this->allOfTimeReminderDate;
	}

	/**
	 * @return mixed
	 */
	public function setAllOfTimeReminderDate(?\DateTimeInterface $allOfTimeReminderDate): self
	{
		$this->allOfTimeReminderDate = $allOfTimeReminderDate;

		return $this;
	}

	public function getJobInvoicingOption(): ?string
	{
		return $this->jobInvoicingOption;
	}

	/**
	 * @return mixed
	 */
	public function setJobInvoicingOption(string $jobInvoicingOption): self
	{
		$this->jobInvoicingOption = $jobInvoicingOption;

		return $this;
	}

	public function getNotesFromVendorToOthers(): ?string
	{
		return $this->notesFromVendorToOthers;
	}

	/**
	 * @return mixed
	 */
	public function setNotesFromVendorToOthers(?string $notesFromVendorToOthers): self
	{
		$this->notesFromVendorToOthers = $notesFromVendorToOthers;

		return $this;
	}

	public function getVisibleInVp(): ?bool
	{
		return $this->visibleInVp;
	}

	/**
	 * @return mixed
	 */
	public function setVisibleInVp(bool $visibleInVp): self
	{
		$this->visibleInVp = $visibleInVp;

		return $this;
	}

	public function getAssignedPersonId(): ?string
	{
		return $this->assignedPersonId;
	}

	/**
	 * @return mixed
	 */
	public function setAssignedPersonId(?string $assignedPersonId): self
	{
		$this->assignedPersonId = $assignedPersonId;

		return $this;
	}

	public function getBackupOldExchangeRatioNotUsed(): ?string
	{
        return (string)$this->backupOldExchangeRatioNotUsed;
	}

	/**
	 * @return mixed
	 */
	public function setBackupOldExchangeRatioNotUsed(?string $backupOldExchangeRatioNotUsed): self
	{
		$this->backupOldExchangeRatioNotUsed = $backupOldExchangeRatioNotUsed;

		return $this;
	}

	public function getExchangeRatioEvent(): ?string
	{
		return $this->exchangeRatioEvent;
	}

	/**
	 * @return mixed
	 */
	public function setExchangeRatioEvent(?string $exchangeRatioEvent): self
	{
		$this->exchangeRatioEvent = $exchangeRatioEvent;

		return $this;
	}

	public function getQualityAnalysisScore(): ?string
	{
		return (string)$this->qualityAnalysisScore;
	}

	/**
	 * @return mixed
	 */
	public function setQualityAnalysisScore(?string $qualityAnalysisScore): self
	{
		$this->qualityAnalysisScore = $qualityAnalysisScore;

		return $this;
	}

	public function getProviderEvaluationResponsiveness(): ?int
	{
		return (int)$this->providerEvaluationResponsiveness;
	}

	/**
	 * @return mixed
	 */
	public function setProviderEvaluationResponsiveness(?int $providerEvaluationResponsiveness): self
	{
		$this->providerEvaluationResponsiveness = $providerEvaluationResponsiveness;

		return $this;
	}

	public function getProviderEvaluationCollaboration(): ?int
	{
        return (int)$this->providerEvaluationCollaboration;
	}

	/**
	 * @return mixed
	 */
	public function setProviderEvaluationCollaboration(?int $providerEvaluationCollaboration): self
	{
		$this->providerEvaluationCollaboration = $providerEvaluationCollaboration;

		return $this;
	}

	public function getActivityType(): ?ActivityType
	{
		return $this->activityType;
	}

	/**
	 * @return mixed
	 */
	public function setActivityType(?ActivityType $activityType): self
	{
		$this->activityType = $activityType;

		return $this;
	}

	public function getContactPerson(): ?ProviderPerson
	{
		return $this->contactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setContactPerson(?ProviderPerson $contactPerson): self
	{
		$this->contactPerson = $contactPerson;

		return $this;
	}

	public function getCurrency(): ?Currency
	{
		return $this->currency;
	}

	/**
	 * @return mixed
	 */
	public function setCurrency(?Currency $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getWorkflowJobInstce(): ?WorkflowJobInstance
	{
		return $this->workflowJobInstce;
	}

	/**
	 * @return mixed
	 */
	public function setWorkflowJobInstce(?WorkflowJobInstance $workflowJobInstce): self
	{
		$this->workflowJobInstce = $workflowJobInstce;

		return $this;
	}

	public function getProviderInvoice(): ?ProviderInvoice
	{
		return $this->providerInvoice;
	}

	/**
	 * @return mixed
	 */
	public function setProviderInvoice(?ProviderInvoice $providerInvoice): self
	{
		$this->providerInvoice = $providerInvoice;

		return $this;
	}

	public function getPaymentConditions(): ?PaymentCondition
	{
		return $this->paymentConditions;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentConditions(?PaymentCondition $paymentConditions): self
	{
		$this->paymentConditions = $paymentConditions;

		return $this;
	}

	public function getTask(): ?Task
	{
		return $this->task;
	}

	/**
	 * @return mixed
	 */
	public function setTask(?Task $task): self
	{
		$this->task = $task;

		return $this;
	}

	public function getVatRate(): ?VatRate
	{
		return $this->vatRate;
	}

	/**
	 * @return mixed
	 */
	public function setVatRate(?VatRate $vatRate): self
	{
		$this->vatRate = $vatRate;

		return $this;
	}

	public function getCustomFields(): ?CustomField
	{
		return $this->customFields;
	}

	/**
	 * @return mixed
	 */
	public function setCustomFields(CustomField $customFields): self
	{
		$this->customFields = $customFields;

		return $this;
	}

	public function getAmountModifiersList(): Collection
	{
		return $this->amountModifiersList;
	}

	/**
	 * @return mixed
	 */
	public function addAmountModifiersList(ActivityAmountModifier $amountModifiersList): self
	{
		if (!$this->amountModifiersList->contains($amountModifiersList)) {
			$this->amountModifiersList[] = $amountModifiersList;
			$amountModifiersList->setActivity($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeAmountModifiersList(ActivityAmountModifier $amountModifiersList): self
	{
		if ($this->amountModifiersList->contains($amountModifiersList)) {
			$this->amountModifiersList->removeElement($amountModifiersList);
			// set the owning side to null (unless already changed)
			if ($amountModifiersList->getActivity() === $this) {
				$amountModifiersList->setActivity(null);
			}
		}

		return $this;
	}

	public function getActivities(): Collection
	{
		return $this->activities;
	}

	/**
	 * @return mixed
	 */
	public function addActivity(Activity $activity): self
	{
		if (!$this->activities->contains($activity)) {
			$this->activities[] = $activity;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeActivity(Activity $activity): self
	{
		if ($this->activities->contains($activity)) {
			$this->activities->removeElement($activity);
		}

		return $this;
	}

	public function getPreviousActivities(): Collection
	{
		return $this->previousActivities;
	}

	/**
	 * @return mixed
	 */
	public function addPreviousActivity(Activity $previousActivity): self
	{
		if (!$this->previousActivities->contains($previousActivity)) {
			$this->previousActivities[] = $previousActivity;
			$previousActivity->addActivity($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removePreviousActivity(Activity $previousActivity): self
	{
		if ($this->previousActivities->contains($previousActivity)) {
			$this->previousActivities->removeElement($previousActivity);
			$previousActivity->removeActivity($this);
		}

		return $this;
	}

	public function getActivityCatCharge(): Collection
	{
		return $this->activityCatCharge;
	}

	/**
	 * @return mixed
	 */
	public function addActivityCatCharge(ActivityCatCharge $activityCatCharge): self
	{
		if (!$this->activityCatCharge->contains($activityCatCharge)) {
			$this->activityCatCharge[] = $activityCatCharge;
			$activityCatCharge->setActivity($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeActivityCatCharge(ActivityCatCharge $activityCatCharge): self
	{
		if ($this->activityCatCharge->contains($activityCatCharge)) {
			$this->activityCatCharge->removeElement($activityCatCharge);
			// set the owning side to null (unless already changed)
			if ($activityCatCharge->getActivity() === $this) {
				$activityCatCharge->setActivity(null);
			}
		}

		return $this;
	}

	public function getActivityCharge(): Collection
	{
		return $this->activityCharge;
	}

	/**
	 * @return mixed
	 */
	public function addActivityCharge(ActivityCharge $activityCharge): self
	{
		if (!$this->activityCharge->contains($activityCharge)) {
			$this->activityCharge[] = $activityCharge;
			$activityCharge->setActivity($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeActivityCharge(ActivityCharge $activityCharge): self
	{
		if ($this->activityCharge->contains($activityCharge)) {
			$this->activityCharge->removeElement($activityCharge);
			// set the owning side to null (unless already changed)
			if ($activityCharge->getActivity() === $this) {
				$activityCharge->setActivity(null);
			}
		}

		return $this;
	}

	public function getActivityName(): ?string
	{
		return $this->activityName;
	}

	/**
	 * @return mixed
	 */
	public function setActivityName(string $activityName): self
	{
		$this->activityName = $activityName;

		return $this;
	}

	public function getProvider(): ?Provider
	{
		return $this->provider;
	}

	/**
	 * @return mixed
	 */
	public function setProvider(?Provider $provider): self
	{
		$this->provider = $provider;

		return $this;
	}

	public function getPurchaseOrderSentDate(): ?\DateTimeInterface
	{
		return $this->purchaseOrderSentDate;
	}

	/**
	 * @return mixed
	 */
	public function setPurchaseOrderSentDate(?\DateTimeInterface $purchaseOrderSentDate): self
	{
		$this->purchaseOrderSentDate = $purchaseOrderSentDate;

		return $this;
	}

	public function getAutoCorrectFilePolicy(): ?bool
	{
		return $this->autoCorrectFilePolicy;
	}

	/**
	 * @return mixed
	 */
	public function setAutoCorrectFilePolicy(?bool $autoCorrectFilePolicy): self
	{
		$this->autoCorrectFilePolicy = $autoCorrectFilePolicy;

		return $this;
	}

	public function getProviderEvaluationOnTimeDelivery(): ?string
	{
		return $this->providerEvaluationOnTimeDelivery;
	}

	/**
	 * @return mixed
	 */
	public function setProviderEvaluationOnTimeDelivery(?string $providerEvaluationOnTimeDelivery): self
	{
		$this->providerEvaluationOnTimeDelivery = $providerEvaluationOnTimeDelivery;

		return $this;
	}

	public function getProviderEvaluationInstructions(): ?string
	{
		return $this->providerEvaluationInstructions;
	}

	/**
	 * @return mixed
	 */
	public function setProviderEvaluationInstructions(?string $providerEvaluationInstructions): self
	{
		$this->providerEvaluationInstructions = $providerEvaluationInstructions;

		return $this;
	}

	public function getAnalyticsProjects(): Collection
	{
		return $this->analyticsProjects;
	}

	/**
	 * @return mixed
	 */
	public function addAnalyticsProject(AnalyticsProject $analyticsProject): self
	{
		if (!$this->analyticsProjects->contains($analyticsProject)) {
			$this->analyticsProjects[] = $analyticsProject;
			$analyticsProject->setJob($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeAnalyticsProject(AnalyticsProject $analyticsProject): self
	{
		if ($this->analyticsProjects->removeElement($analyticsProject)) {
			// set the owning side to null (unless already changed)
			if ($analyticsProject->getJob() === $this) {
				$analyticsProject->setJob(null);
			}
		}

		return $this;
	}
}
