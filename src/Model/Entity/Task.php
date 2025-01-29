<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[\AllowDynamicProperties] #[ORM\Table(name: 'task')]
#[ORM\UniqueConstraint(name: 'task_quote_phase_id_number_key', columns: ['quote_phase_id_number'])]
#[ORM\UniqueConstraint(name: 'task_project_phase_id_number_key', columns: ['project_phase_id_number'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\TaskRepository')]
class Task implements EntityInterface
{
	public const STATUS_READY = 'READY';
	public const STATUS_OPENED = 'OPENED';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'task_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'task_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'date_of_event', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateOfEvent;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'customer_special_instructions', type: 'text', nullable: true)]
	private ?string $customerSpecialInstructions;

	#[ORM\Column(name: 'customer_task_number', type: 'string', nullable: true)]
	private ?string $customerTaskNumber;

	#[ORM\Column(name: 'internal_special_instructions', type: 'text', nullable: true)]
	private ?string $internalSpecialInstructions;

	#[ORM\Column(name: 'invoiceable', type: 'boolean', nullable: true)]
	private ?bool $invoiceable;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'payment_note', type: 'text', nullable: true)]
	private ?string $paymentNote;

	#[ORM\Column(name: 'place', type: 'string', nullable: true)]
	private ?string $place;

	#[ORM\Column(name: 'provider_special_instructions', type: 'text', nullable: true)]
	private ?string $providerSpecialInstructions;

	#[ORM\Column(name: 'purpose_and_use_of_translation', type: 'text', nullable: true)]
	private ?string $purposeAndUseOfTranslation;

	#[ORM\Column(name: 'quick_note', type: 'boolean', nullable: false)]
	private bool $quickNote;

	#[ORM\Column(name: 'sent_on_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $sentOnDate;

	#[ORM\Column(name: 'style', type: 'text', nullable: true)]
	private ?string $style;

	#[ORM\Column(name: 'target_audience', type: 'text', nullable: true)]
	private ?string $targetAudience;

	#[ORM\Column(name: 'dictionary_directory', type: 'string', length: 2000, nullable: true)]
	private ?string $dictionaryDirectory;

	#[ORM\Column(name: 'log_directory', type: 'string', length: 2000, nullable: true)]
	private ?string $logDirectory;

	#[ORM\Column(name: 'ready_directory', type: 'string', length: 2000, nullable: true)]
	private ?string $readyDirectory;

	#[ORM\Column(name: 'reference_directory', type: 'string', length: 2000, nullable: true)]
	private ?string $referenceDirectory;

	#[ORM\Column(name: 'tm_directory', type: 'string', length: 2000, nullable: true)]
	private ?string $tmDirectory;

	#[ORM\Column(name: 'workfile_directory', type: 'string', length: 2000, nullable: true)]
	private ?string $workfileDirectory;

	#[ORM\Column(name: 'order_confirmation_recipient_person_type', type: 'string', nullable: true)]
	private ?string $orderConfirmationRecipientPersonType;

	#[ORM\Column(name: 'activities_status', type: 'string', nullable: false)]
	private string $activitiesStatus;

	#[ORM\Column(name: 'auto_calculate_payment_dates', type: 'boolean', nullable: true)]
	private ?bool $autoCalculatePaymentDates;

	#[ORM\Column(name: 'confirmed_files_downloading', type: 'boolean', nullable: true)]
	private ?bool $confirmedFilesDownloading;

	#[ORM\Column(name: 'delivery_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $deliveryDate;

	#[ORM\Column(name: 'actual_start_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $actualStartDate;

	#[ORM\Column(name: 'close_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $closeDate;

	#[ORM\Column(name: 'deadline', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $deadline;

	#[ORM\Column(name: 'start_date', type: 'datetime', nullable: false)]
	private \DateTimeInterface $startDate;

	#[ORM\Column(name: 'draft_invoice_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $draftInvoiceDate;

	#[ORM\Column(name: 'final_invoice_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $finalInvoiceDate;

	#[ORM\Column(name: 'invoice_task_position', type: 'integer', nullable: true)]
	private ?int $invoiceTaskPosition;

	#[ORM\Column(name: 'payment_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $paymentDate;

	#[ORM\Column(name: 'project_phase_id_number', type: 'string', nullable: true)]
	private ?string $projectPhaseIdNumber;

	#[ORM\Column(name: 'project_task', type: 'boolean', nullable: true)]
	private ?bool $projectTask;

	#[ORM\Column(name: 'estimated_delivery_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $estimatedDeliveryDate;

	#[ORM\Column(name: 'working_days', type: 'integer', nullable: true)]
	private ?int $workingDays;

	#[ORM\Column(name: 'working_files_number', type: 'integer', nullable: true)]
	private ?int $workingFilesNumber;

	#[ORM\Column(name: 'ontime_status', type: 'string', nullable: true)]
	private ?string $ontimeStatus;

	#[ORM\Column(name: 'partial_delivery_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $partialDeliveryDate;

	#[ORM\Column(name: 'quote_phase_id_number', type: 'string', nullable: true)]
	private ?string $quotePhaseIdNumber;

	#[ORM\Column(name: 'receivables_status', type: 'string', nullable: false)]
	private string $receivablesStatus;

	#[ORM\Column(name: 'remote_project_id', type: 'text', nullable: true)]
	private ?string $remoteProjectId;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'customer_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $customerContactPerson;

	#[ORM\ManyToOne(targetEntity: ExternalSystemProject::class)]
	#[ORM\JoinColumn(name: 'external_system_project_id', referencedColumnName: 'external_system_project_id', nullable: true)]
	private ?ExternalSystemProject $externalSystemProject;

	#[ORM\ManyToOne(targetEntity: LanguageSpecialization::class)]
	#[ORM\JoinColumn(name: 'language_specialization_id', referencedColumnName: 'language_specialization_id', nullable: false)]
	private LanguageSpecialization $specialization;

	#[ORM\Column(name: 'provider_selection_settings_id', type: 'bigint', nullable: true)]
	private ?string $providerSelectionSettingsId;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'send_back_to_customer_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $sendBackToCustomerContactPerson;

	#[ORM\ManyToOne(targetEntity: VatRate::class)]
	#[ORM\JoinColumn(name: 'vat_rate_id', referencedColumnName: 'vat_rate_id', nullable: false)]
	private VatRate $vatRate;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $workflow;

	#[ORM\Column(name: 'workflow_definition_id', type: 'bigint', nullable: true)]
	private ?string $workflowDefinitionId;

	#[ORM\Column(name: 'bundles_meta_directory_when_embeeded_id', type: 'bigint', nullable: true)]
	private ?string $bundlesMetaDirectoryWhenEmbeededId;

	#[ORM\ManyToOne(targetEntity: CustomerInvoice::class, inversedBy: 'tasks')]
	#[ORM\JoinColumn(name: 'customer_invoice_id', referencedColumnName: 'customer_invoice_id', nullable: true)]
	private ?CustomerInvoice $customerInvoice;

	#[ORM\ManyToOne(targetEntity: PaymentCondition::class)]
	#[ORM\JoinColumn(name: 'payment_conditions_id', referencedColumnName: 'payment_conditions_id', nullable: true)]
	private ?PaymentCondition $paymentConditions;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', nullable: true)]
	private ?Project $project;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'project_coordinator_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $projectCoordinator;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'project_manager_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $projectManager;

	#[ORM\ManyToOne(targetEntity: TaskFinance::class)]
	#[ORM\JoinColumn(name: 'project_part_finance_id', referencedColumnName: 'task_finance_id', nullable: true)]
	private ?TaskFinance $projectPartFinance;

	#[ORM\Column(name: 'project_part_template_id', type: 'bigint', nullable: true)]
	private ?string $projectPartTemplateId;

	#[ORM\ManyToOne(targetEntity: TaskFinance::class)]
	#[ORM\JoinColumn(name: 'quote_part_finance_id', referencedColumnName: 'task_finance_id', nullable: true)]
	private ?TaskFinance $quotePartFinanceId;

	#[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'tasks')]
	#[ORM\JoinColumn(name: 'quote_id', referencedColumnName: 'quote_id', nullable: true)]
	private ?Quote $quote;

	#[ORM\Column(name: 'link_parent_job_started', type: 'boolean', nullable: true)]
	private ?bool $linkParentJobStarted;

	#[ORM\Column(name: 'link_parent_job_id', type: 'text', nullable: true)]
	private ?string $linkParentJob;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\Column(name: 'activities_total', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $activitiesTotal;

	#[ORM\Column(name: 'total_cost', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $totalCost;

	#[ORM\Column(name: 'wa_translator_name', type: 'text', nullable: true)]
	private ?string $waTranslatorName;

	#[ORM\Column(name: 'wa_reviewer_name', type: 'text', nullable: true)]
	private ?string $waReviewerName;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: Language::class)]
	#[ORM\JoinColumn(name: 'source_ref_language_id', referencedColumnName: 'id', nullable: true)]
	private ?Language $sourceRefLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;

	#[ORM\ManyToOne(targetEntity: Language::class)]
	#[ORM\JoinColumn(name: 'target_ref_language_id', referencedColumnName: 'id', nullable: true)]
	private ?Language $targetRefLanguage;

	#[ORM\Column(name: 'task_workflow_job_instance_when_embeeded_id', type: 'bigint', nullable: true)]
	private ?string $taskWorkflowJobInstanceWhenEmbeededId;

	#[ORM\Column(name: 'project_coordinator_deadline_reminder_id', type: 'bigint', nullable: true)]
	private ?string $projectCoordinatorDeadlineReminderId;

	#[ORM\Column(name: 'project_manager_deadline_reminder_id', type: 'bigint', nullable: true)]
	private ?string $projectManagerDeadlineReminderId;

	#[ORM\Column(name: 'quote_part_template_id', type: 'bigint', nullable: true)]
	private ?string $quotePartTemplateId;

	#[ORM\Column(name: 'total_activities', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $totalActivities;

	#[ORM\Column(name: 'progress_activities', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $progressActivities;

	#[ORM\Column(name: 'total_agreed', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => 0])]
	private ?float $totalAgreed;

	#[ORM\Column(name: 'minimum', type: 'boolean', nullable: true, options: ['default' => 'false'])]
	private ?bool $minimum;

	#[ORM\Column(name: 'rentability', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $rentability;

	#[ORM\Column(name: 'tm_savings', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $tmSavings;

	#[ORM\Column(name: 'margin', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $margin;

	#[ORM\Column(name: 'total_amount_modifier', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $totalAmountModifier;

	#[ORM\Column(name: 'time_based_cost', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $timeBasedCost;

	#[ORM\Column(name: 'account_status', type: 'string', nullable: true)]
	private ?string $accountStatus;

	#[ORM\Column(name: 'provider_invoice_payment_status', type: 'string', nullable: true)]
	private ?string $providerInvoicePaymentStatus;

	#[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
	#[ORM\OrderBy(['id' => 'ASC'])]
	private mixed $activities;

	#[ORM\JoinTable(name: 'task_categories')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'project_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'tasks')]
	protected mixed $categories;

	#[ORM\ManyToMany(targetEntity: Feedback::class, mappedBy: 'tasks', cascade: ['persist'])]
	private mixed $feedbacks;

	#[ORM\OneToMany(targetEntity: AnalyticsProject::class, mappedBy: 'task', cascade: ['persist', 'detach'])]
	private mixed $analyticsProjects;

	#[ORM\JoinTable(name: 'task_additional_contact_persons')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'person_id', referencedColumnName: 'contact_person_id')]
	#[ORM\ManyToMany(targetEntity: CustomerPerson::class, cascade: ['persist'], inversedBy: 'tasks')]
	protected mixed $persons;

	#[ORM\OneToMany(targetEntity: WorkflowJobFile::class, mappedBy: 'task')]
	private mixed $workflowJobFiles;

	#[ORM\OneToMany(targetEntity: TaskReview::class, mappedBy: 'task')]
	private mixed $taskForReview;

	public function __construct()
	{
		$this->activities = new ArrayCollection();
		$this->categories = new ArrayCollection();
		$this->feedbacks = new ArrayCollection();
		$this->analyticsProjects = new ArrayCollection();
		$this->persons = new ArrayCollection();
		$this->workflowJobFiles = new ArrayCollection();
		$this->taskForReview = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function hasReviewActivity(): bool
	{
		$response = false;

		/** @var Activity $activity */
		foreach ($this->activities as $activity) {
			if (ActivityType::TYPE_REVIEW == $activity?->getActivityType()->getId() && Activity::STATUS_STARTED === $activity->getStatus()) {
				$response = true;
				break;
			}
		}

		return $response;
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

	public function getDateOfEvent(): ?\DateTimeInterface
	{
		return $this->dateOfEvent;
	}

	/**
	 * @return mixed
	 */
	public function setDateOfEvent(?\DateTimeInterface $dateOfEvent): self
	{
		$this->dateOfEvent = $dateOfEvent;

		return $this;
	}

	public function getStandardPropertyContainerId(): ?string
	{
		return $this->standardPropertyContainerId;
	}

	/**
	 * @return mixed
	 */
	public function setStandardPropertyContainerId(?string $standardPropertyContainerId): self
	{
		$this->standardPropertyContainerId = $standardPropertyContainerId;

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

	public function getCustomerTaskNumber(): ?string
	{
		return $this->customerTaskNumber;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerTaskNumber(?string $customerTaskNumber): self
	{
		$this->customerTaskNumber = $customerTaskNumber;

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

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getNotes(): ?string
	{
		return $this->notes;
	}

	/**
	 * @return mixed
	 */
	public function setNotes(?string $notes): self
	{
		$this->notes = $notes;

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

	public function getPlace(): ?string
	{
		return $this->place;
	}

	/**
	 * @return mixed
	 */
	public function setPlace(?string $place): self
	{
		$this->place = $place;

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

	public function getPurposeAndUseOfTranslation(): ?string
	{
		return $this->purposeAndUseOfTranslation;
	}

	/**
	 * @return mixed
	 */
	public function setPurposeAndUseOfTranslation(?string $purposeAndUseOfTranslation): self
	{
		$this->purposeAndUseOfTranslation = $purposeAndUseOfTranslation;

		return $this;
	}

	public function getQuickNote(): ?bool
	{
		return $this->quickNote;
	}

	/**
	 * @return mixed
	 */
	public function setQuickNote(bool $quickNote): self
	{
		$this->quickNote = $quickNote;

		return $this;
	}

	public function getSentOnDate(): ?\DateTimeInterface
	{
		return $this->sentOnDate;
	}

	/**
	 * @return mixed
	 */
	public function setSentOnDate(?\DateTimeInterface $sentOnDate): self
	{
		$this->sentOnDate = $sentOnDate;

		return $this;
	}

	public function getStyle(): ?string
	{
		return $this->style;
	}

	/**
	 * @return mixed
	 */
	public function setStyle(?string $style): self
	{
		$this->style = $style;

		return $this;
	}

	public function getTargetAudience(): ?string
	{
		return $this->targetAudience;
	}

	/**
	 * @return mixed
	 */
	public function setTargetAudience(?string $targetAudience): self
	{
		$this->targetAudience = $targetAudience;

		return $this;
	}

	public function getDictionaryDirectory(): ?string
	{
		return $this->dictionaryDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setDictionaryDirectory(?string $dictionaryDirectory): self
	{
		$this->dictionaryDirectory = $dictionaryDirectory;

		return $this;
	}

	public function getLogDirectory(): ?string
	{
		return $this->logDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setLogDirectory(?string $logDirectory): self
	{
		$this->logDirectory = $logDirectory;

		return $this;
	}

	public function getReadyDirectory(): ?string
	{
		return $this->readyDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setReadyDirectory(?string $readyDirectory): self
	{
		$this->readyDirectory = $readyDirectory;

		return $this;
	}

	public function getReferenceDirectory(): ?string
	{
		return $this->referenceDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setReferenceDirectory(?string $referenceDirectory): self
	{
		$this->referenceDirectory = $referenceDirectory;

		return $this;
	}

	public function getTmDirectory(): ?string
	{
		return $this->tmDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setTmDirectory(?string $tmDirectory): self
	{
		$this->tmDirectory = $tmDirectory;

		return $this;
	}

	public function getWorkfileDirectory(): ?string
	{
		return $this->workfileDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setWorkfileDirectory(?string $workfileDirectory): self
	{
		$this->workfileDirectory = $workfileDirectory;

		return $this;
	}

	public function getOrderConfirmationRecipientPersonType(): ?string
	{
		return $this->orderConfirmationRecipientPersonType;
	}

	/**
	 * @return mixed
	 */
	public function setOrderConfirmationRecipientPersonType(?string $orderConfirmationRecipientPersonType): self
	{
		$this->orderConfirmationRecipientPersonType = $orderConfirmationRecipientPersonType;

		return $this;
	}

	public function getActivitiesStatus(): ?string
	{
		return $this->activitiesStatus;
	}

	/**
	 * @return mixed
	 */
	public function setActivitiesStatus(string $activitiesStatus): self
	{
		$this->activitiesStatus = $activitiesStatus;

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

	public function getConfirmedFilesDownloading(): ?bool
	{
		return $this->confirmedFilesDownloading;
	}

	/**
	 * @return mixed
	 */
	public function setConfirmedFilesDownloading(?bool $confirmedFilesDownloading): self
	{
		$this->confirmedFilesDownloading = $confirmedFilesDownloading;

		return $this;
	}

	public function getDeliveryDate(): ?\DateTimeInterface
	{
		return $this->deliveryDate;
	}

	/**
	 * @return mixed
	 */
	public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
	{
		$this->deliveryDate = $deliveryDate;

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

	public function getInvoiceTaskPosition(): ?int
	{
		return $this->invoiceTaskPosition;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceTaskPosition(?int $invoiceTaskPosition): self
	{
		$this->invoiceTaskPosition = $invoiceTaskPosition;

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

	public function getProjectTask(): ?bool
	{
		return $this->projectTask;
	}

	/**
	 * @return mixed
	 */
	public function setProjectTask(?bool $projectTask): self
	{
		$this->projectTask = $projectTask;

		return $this;
	}

	public function getEstimatedDeliveryDate(): ?\DateTimeInterface
	{
		return $this->estimatedDeliveryDate;
	}

	/**
	 * @return mixed
	 */
	public function setEstimatedDeliveryDate(?\DateTimeInterface $estimatedDeliveryDate): self
	{
		$this->estimatedDeliveryDate = $estimatedDeliveryDate;

		return $this;
	}

	public function getWorkingDays(): ?int
	{
		return $this->workingDays;
	}

	/**
	 * @return mixed
	 */
	public function setWorkingDays(?int $workingDays): self
	{
		$this->workingDays = $workingDays;

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

	public function getReceivablesStatus(): ?string
	{
		return $this->receivablesStatus;
	}

	/**
	 * @return mixed
	 */
	public function setReceivablesStatus(string $receivablesStatus): self
	{
		$this->receivablesStatus = $receivablesStatus;

		return $this;
	}

	public function getRemoteProjectId(): ?string
	{
		return $this->remoteProjectId;
	}

	/**
	 * @return mixed
	 */
	public function setRemoteProjectId(?string $remoteProjectId): self
	{
		$this->remoteProjectId = $remoteProjectId;

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

	public function getWorkflowDefinitionId(): ?string
	{
		return $this->workflowDefinitionId;
	}

	/**
	 * @return mixed
	 */
	public function setWorkflowDefinitionId(?string $workflowDefinitionId): self
	{
		$this->workflowDefinitionId = $workflowDefinitionId;

		return $this;
	}

	public function getBundlesMetaDirectoryWhenEmbeededId(): ?string
	{
		return $this->bundlesMetaDirectoryWhenEmbeededId;
	}

	/**
	 * @return mixed
	 */
	public function setBundlesMetaDirectoryWhenEmbeededId(?string $bundlesMetaDirectoryWhenEmbeededId): self
	{
		$this->bundlesMetaDirectoryWhenEmbeededId = $bundlesMetaDirectoryWhenEmbeededId;

		return $this;
	}

	public function getProjectPartTemplateId(): ?string
	{
		return $this->projectPartTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setProjectPartTemplateId(?string $projectPartTemplateId): self
	{
		$this->projectPartTemplateId = $projectPartTemplateId;

		return $this;
	}

	public function getLinkParentJobStarted(): ?bool
	{
		return $this->linkParentJobStarted;
	}

	/**
	 * @return mixed
	 */
	public function setLinkParentJobStarted(?bool $linkParentJobStarted): self
	{
		$this->linkParentJobStarted = $linkParentJobStarted;

		return $this;
	}

	public function getLinkParentJob(): ?string
	{
		return $this->linkParentJob;
	}

	/**
	 * @return mixed
	 */
	public function setLinkParentJob(?string $linkParentJob): self
	{
		$this->linkParentJob = $linkParentJob;

		return $this;
	}

	public function getActivitiesTotal(): ?string
	{
		return $this->activitiesTotal;
	}

	/**
	 * @return mixed
	 */
	public function setActivitiesTotal(?string $activitiesTotal): self
	{
		$this->activitiesTotal = $activitiesTotal;

		return $this;
	}

	public function getTaskWorkflowJobInstanceWhenEmbeededId(): ?string
	{
		return $this->taskWorkflowJobInstanceWhenEmbeededId;
	}

	/**
	 * @return mixed
	 */
	public function setTaskWorkflowJobInstanceWhenEmbeededId(?string $taskWorkflowJobInstanceWhenEmbeededId): self
	{
		$this->taskWorkflowJobInstanceWhenEmbeededId = $taskWorkflowJobInstanceWhenEmbeededId;

		return $this;
	}

	public function getProjectCoordinatorDeadlineReminderId(): ?string
	{
		return $this->projectCoordinatorDeadlineReminderId;
	}

	/**
	 * @return mixed
	 */
	public function setProjectCoordinatorDeadlineReminderId(?string $projectCoordinatorDeadlineReminderId): self
	{
		$this->projectCoordinatorDeadlineReminderId = $projectCoordinatorDeadlineReminderId;

		return $this;
	}

	public function getProjectManagerDeadlineReminderId(): ?string
	{
		return $this->projectManagerDeadlineReminderId;
	}

	/**
	 * @return mixed
	 */
	public function setProjectManagerDeadlineReminderId(?string $projectManagerDeadlineReminderId): self
	{
		$this->projectManagerDeadlineReminderId = $projectManagerDeadlineReminderId;

		return $this;
	}

	public function getQuotePartTemplateId(): ?string
	{
		return $this->quotePartTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setQuotePartTemplateId(?string $quotePartTemplateId): self
	{
		$this->quotePartTemplateId = $quotePartTemplateId;

		return $this;
	}

	public function getTotalAgreed(): ?string
	{
		return $this->totalAgreed;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAgreed(?string $totalAgreed): self
	{
		$this->totalAgreed = $totalAgreed;

		return $this;
	}

	public function getRentability(): ?string
	{
		return $this->rentability;
	}

	/**
	 * @return mixed
	 */
	public function setRentability(?string $rentability): self
	{
		$this->rentability = $rentability;

		return $this;
	}

	public function getTmSavings(): ?string
	{
		return $this->tmSavings;
	}

	/**
	 * @return mixed
	 */
	public function setTmSavings(?string $tmSavings): self
	{
		$this->tmSavings = $tmSavings;

		return $this;
	}

	public function getTimeBasedCost(): ?string
	{
		return $this->timeBasedCost;
	}

	/**
	 * @return mixed
	 */
	public function setTimeBasedCost(?string $timeBasedCost): self
	{
		$this->timeBasedCost = $timeBasedCost;

		return $this;
	}

	public function getAccountStatus(): ?string
	{
		return $this->accountStatus;
	}

	/**
	 * @return mixed
	 */
	public function setAccountStatus(?string $accountStatus): self
	{
		$this->accountStatus = $accountStatus;

		return $this;
	}

	public function getProviderInvoicePaymentStatus(): ?string
	{
		return $this->providerInvoicePaymentStatus;
	}

	/**
	 * @return mixed
	 */
	public function setProviderInvoicePaymentStatus(?string $providerInvoicePaymentStatus): self
	{
		$this->providerInvoicePaymentStatus = $providerInvoicePaymentStatus;

		return $this;
	}

	public function getCustomerContactPerson(): ?CustomerPerson
	{
		return $this->customerContactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerContactPerson(?CustomerPerson $customerContactPerson): self
	{
		$this->customerContactPerson = $customerContactPerson;

		return $this;
	}

	public function getExternalSystemProject(): ?ExternalSystemProject
	{
		return $this->externalSystemProject;
	}

	/**
	 * @return mixed
	 */
	public function setExternalSystemProject(?ExternalSystemProject $externalSystemProject): self
	{
		$this->externalSystemProject = $externalSystemProject;

		return $this;
	}

	public function getSpecialization(): ?LanguageSpecialization
	{
		return $this->specialization;
	}

	/**
	 * @return mixed
	 */
	public function setSpecialization(?LanguageSpecialization $specialization): self
	{
		$this->specialization = $specialization;

		return $this;
	}

	public function getSendBackToCustomerContactPerson(): ?CustomerPerson
	{
		return $this->sendBackToCustomerContactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setSendBackToCustomerContactPerson(?CustomerPerson $sendBackToCustomerContactPerson): self
	{
		$this->sendBackToCustomerContactPerson = $sendBackToCustomerContactPerson;

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

	public function getWorkflow(): ?Workflow
	{
		return $this->workflow;
	}

	/**
	 * @return mixed
	 */
	public function setWorkflow(?Workflow $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}

	public function getCustomerInvoice(): ?CustomerInvoice
	{
		return $this->customerInvoice;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerInvoice(?CustomerInvoice $customerInvoice): self
	{
		$this->customerInvoice = $customerInvoice;

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

	public function getProject(): ?Project
	{
		return $this->project;
	}

	/**
	 * @return mixed
	 */
	public function setProject(?Project $project): self
	{
		$this->project = $project;

		return $this;
	}

	public function getProjectCoordinator(): ?User
	{
		return $this->projectCoordinator;
	}

	/**
	 * @return mixed
	 */
	public function setProjectCoordinator(?User $projectCoordinator): self
	{
		$this->projectCoordinator = $projectCoordinator;

		return $this;
	}

	public function getProjectManager(): ?User
	{
		return $this->projectManager;
	}

	/**
	 * @return mixed
	 */
	public function setProjectManager(?User $projectManager): self
	{
		$this->projectManager = $projectManager;

		return $this;
	}

	public function getProjectPartFinance(): ?TaskFinance
	{
		return $this->projectPartFinance;
	}

	/**
	 * @return mixed
	 */
	public function setProjectPartFinance(?TaskFinance $projectPartFinance): self
	{
		$this->projectPartFinance = $projectPartFinance;

		return $this;
	}

	public function getQuotePartFinanceId(): ?TaskFinance
	{
		return $this->quotePartFinanceId;
	}

	/**
	 * @return mixed
	 */
	public function setQuotePartFinanceId(?TaskFinance $quotePartFinanceId): self
	{
		$this->quotePartFinanceId = $quotePartFinanceId;

		return $this;
	}

	public function getQuote(): ?Quote
	{
		return $this->quote;
	}

	/**
	 * @return mixed
	 */
	public function setQuote(?Quote $quote): self
	{
		$this->quote = $quote;

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

	public function getSourceLanguage(): ?XtrfLanguage
	{
		return $this->sourceLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setSourceLanguage(?XtrfLanguage $sourceLanguage): self
	{
		$this->sourceLanguage = $sourceLanguage;

		return $this;
	}

	public function getSourceRefLanguage(): ?Language
	{
		return $this->sourceRefLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setSourceRefLanguage(?Language $sourceRefLanguage): self
	{
		$this->sourceRefLanguage = $sourceRefLanguage;

		return $this;
	}

	public function getTargetLanguage(): ?XtrfLanguage
	{
		return $this->targetLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguage(?XtrfLanguage $targetLanguage): self
	{
		$this->targetLanguage = $targetLanguage;

		return $this;
	}

	public function getTargetRefLanguage(): ?Language
	{
		return $this->targetRefLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setTargetRefLanguage(?Language $targetRefLanguage): self
	{
		$this->targetRefLanguage = $targetRefLanguage;

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
			$activity->setTask($this);
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
			// set the owning side to null (unless already changed)
			if ($activity->getTask() === $this) {
				$activity->setTask(null);
			}
		}

		return $this;
	}

	public function getCategories(): Collection
	{
		return $this->categories;
	}

	/**
	 * @return mixed
	 */
	public function addCategory(Category $category): self
	{
		if (!$this->categories->contains($category)) {
			$this->categories[] = $category;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCategory(Category $category): self
	{
		if ($this->categories->contains($category)) {
			$this->categories->removeElement($category);
		}

		return $this;
	}

	public function getFeedbacks(): Collection
	{
		return $this->feedbacks;
	}

	/**
	 * @return mixed
	 */
	public function addFeedback(Feedback $feedback): self
	{
		if (!$this->feedbacks->contains($feedback)) {
			$this->feedbacks[] = $feedback;
			$feedback->addTask($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeFeedback(Feedback $feedback): self
	{
		if ($this->feedbacks->contains($feedback)) {
			$this->feedbacks->removeElement($feedback);
			$feedback->removeTask($this);
		}

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
			$analyticsProject->setTask($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeAnalyticsProject(AnalyticsProject $analyticsProject): self
	{
		if ($this->analyticsProjects->contains($analyticsProject)) {
			$this->analyticsProjects->removeElement($analyticsProject);
			// set the owning side to null (unless already changed)
			if ($analyticsProject->getTask() === $this) {
				$analyticsProject->setTask(null);
			}
		}

		return $this;
	}

	public function getPersons(): Collection
	{
		return $this->persons;
	}

	/**
	 * @return mixed
	 */
	public function addPerson(CustomerPerson $person): self
	{
		if (!$this->persons->contains($person)) {
			$this->persons[] = $person;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removePerson(CustomerPerson $person): self
	{
		if ($this->persons->contains($person)) {
			$this->persons->removeElement($person);
		}

		return $this;
	}

	public function getMargin(): ?string
	{
		return $this->margin;
	}

	/**
	 * @return mixed
	 */
	public function setMargin(?string $margin): self
	{
		$this->margin = $margin;

		return $this;
	}

	public function getWorkingFilesNumber(): ?int
	{
		return $this->workingFilesNumber;
	}

	/**
	 * @return mixed
	 */
	public function setWorkingFilesNumber(?int $workingFilesNumber): self
	{
		$this->workingFilesNumber = $workingFilesNumber;

		return $this;
	}

	public function getOntimeStatus(): ?string
	{
		return $this->ontimeStatus;
	}

	/**
	 * @return mixed
	 */
	public function setOntimeStatus(?string $ontimeStatus): self
	{
		$this->ontimeStatus = $ontimeStatus;

		return $this;
	}

	public function getPartialDeliveryDate(): ?\DateTimeInterface
	{
		return $this->partialDeliveryDate;
	}

	/**
	 * @return mixed
	 */
	public function setPartialDeliveryDate(?\DateTimeInterface $partialDeliveryDate): self
	{
		$this->partialDeliveryDate = $partialDeliveryDate;

		return $this;
	}

	public function getTotalCost(): ?string
	{
		return $this->totalCost;
	}

	/**
	 * @return mixed
	 */
	public function setTotalCost(?string $totalCost): self
	{
		$this->totalCost = $totalCost;

		return $this;
	}

	public function getWaTranslatorName(): ?string
	{
		return $this->waTranslatorName;
	}

	/**
	 * @return mixed
	 */
	public function setWaTranslatorName(?string $waTranslatorName): self
	{
		$this->waTranslatorName = $waTranslatorName;

		return $this;
	}

	public function getWaReviewerName(): ?string
	{
		return $this->waReviewerName;
	}

	/**
	 * @return mixed
	 */
	public function setWaReviewerName(?string $waReviewerName): self
	{
		$this->waReviewerName = $waReviewerName;

		return $this;
	}

	public function getMinimum(): ?bool
	{
		return $this->minimum;
	}

	/**
	 * @return mixed
	 */
	public function setMinimum(?bool $minimum): self
	{
		$this->minimum = $minimum;

		return $this;
	}

	public function getTotalAmountModifier(): ?string
	{
		return $this->totalAmountModifier;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAmountModifier(?string $totalAmountModifier): self
	{
		$this->totalAmountModifier = $totalAmountModifier;

		return $this;
	}

	public function getWorkflowJobFiles(): Collection
	{
		return $this->workflowJobFiles;
	}

	/**
	 * @return mixed
	 */
	public function addWorkflowJobFile(WorkflowJobFile $workflowJobFile): self
	{
		if (!$this->workflowJobFiles->contains($workflowJobFile)) {
			$this->workflowJobFiles[] = $workflowJobFile;
			$workflowJobFile->setTask($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeWorkflowJobFile(WorkflowJobFile $workflowJobFile): self
	{
		if ($this->workflowJobFiles->contains($workflowJobFile)) {
			$this->workflowJobFiles->removeElement($workflowJobFile);
			// set the owning side to null (unless already changed)
			if ($workflowJobFile->getTask() === $this) {
				$workflowJobFile->setTask(null);
			}
		}

		return $this;
	}

	public function getTotalActivities(): ?int
	{
		return $this->totalActivities;
	}

	/**
	 * @return mixed
	 */
	public function setTotalActivities(?int $totalActivities): self
	{
		$this->totalActivities = $totalActivities;

		return $this;
	}

	public function getProgressActivities(): ?int
	{
		return $this->progressActivities;
	}

	/**
	 * @return mixed
	 */
	public function setProgressActivities(?int $progressActivities): self
	{
		$this->progressActivities = $progressActivities;

		return $this;
	}

	public function getTaskForReview(): Collection
	{
		return $this->taskForReview;
	}

	/**
	 * @return mixed
	 */
	public function addTaskForReview(TaskReview $taskForReview): self
	{
		if (!$this->taskForReview->contains($taskForReview)) {
			$this->taskForReview[] = $taskForReview;
			$taskForReview->setTask($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeTaskForReview(TaskReview $taskForReview): self
	{
		if ($this->taskForReview->contains($taskForReview)) {
			$this->taskForReview->removeElement($taskForReview);
			// set the owning side to null (unless already changed)
			if ($taskForReview->getTask() === $this) {
				$taskForReview->setTask(null);
			}
		}

		return $this;
	}
}
