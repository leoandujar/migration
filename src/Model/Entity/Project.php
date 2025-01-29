<?php

namespace App\Model\Entity;

use App\Model\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'project')]
#[ORM\Index(columns: ['status'])]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[\AllowDynamicProperties]
class Project implements EntityInterface
{
	public const STATUS_OPEN = 'OPENED';
	public const STATUS_CLOSED = 'CLOSED';
	public const STATUS_COMPLAINT = 'CLAIM';
	public const STATUS_REVIEW = 'REVIEW';
	public const STATUS_CANCELLED = 'CANCELLED';
	public const STATUS_REQUESTED = 'REQUESTED_PROJECT';

	public const SURVEY_SURVEYED = 'SURVEYED';
	public const SURVEY_NOT_SURVEYED = 'NOT_SURVEYED';
	public const SURVEY_ANY = 'ANY';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'project_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'project_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'customer_project_number', type: 'string', nullable: true)]
	private ?string $customerProjectNumber;

	#[ORM\Column(name: 'customer_special_instructions', type: 'text', nullable: true)]
	private ?string $customerSpecialInstructions;

	#[ORM\Column(name: 'id_number', type: 'string', nullable: false, unique: true)]
	private string $idNumber;

	#[ORM\Column(name: 'internal_special_instructions', type: 'text', nullable: true)]
	private ?string $internalSpecialInstructions;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'order_confirmation_recipient_person_type', type: 'string', nullable: true)]
	private ?string $orderConfirmationRecipient;

	#[ORM\Column(name: 'payment_note', type: 'text', nullable: true)]
	private ?string $paymentNote;

	#[ORM\Column(name: 'place', type: 'string', nullable: true)]
	private ?string $place;

	#[ORM\Column(name: 'project_delivery_method', type: 'string', nullable: true)]
	private ?string $deliveryMethod;

	#[ORM\Column(name: 'project_delivery_settings', type: 'text', nullable: true)]
	private ?string $deliverySettings;

	#[ORM\Column(name: 'provider_special_instructions', type: 'text', nullable: true)]
	private ?string $providerSpecialInstructions;

	#[ORM\Column(name: 'quick_note', type: 'boolean', nullable: false)]
	private bool $quickNote;

	#[ORM\Column(name: 'survey_comment', type: 'text', nullable: true)]
	private ?string $surveyComment;

	#[ORM\Column(name: 'survey_request_date_sent', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $surveyRequestDateSent;

	#[ORM\Column(name: 'survey_sent', type: 'boolean', nullable: true)]
	private ?bool $surveySent;

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

	#[ORM\Column(name: 'sent_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $sentDate;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'account_manager_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $accountManager;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'customer_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $customerContactPerson;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'projects')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\ManyToOne(targetEntity: CustomerPriceProfile::class)]
	#[ORM\JoinColumn(name: 'customer_price_profile_id', referencedColumnName: 'customer_price_profile_id', nullable: true)]
	private ?CustomerPriceProfile $customerPriceProfile;

	#[ORM\ManyToOne(targetEntity: LanguageSpecialization::class)]
	#[ORM\JoinColumn(name: 'language_specialization_id', referencedColumnName: 'language_specialization_id', nullable: true)]
	private ?LanguageSpecialization $specialization;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'project_coordinator_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $projectCoordinator;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'project_manager_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $projectManager;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'send_back_to_customer_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $sendBackToContact;

	#[ORM\Column(name: 'template_id', type: 'bigint', nullable: true)]
	private ?string $templateId;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\OneToOne(targetEntity: Quote::class, cascade: ['persist'], inversedBy: 'project')]
	#[ORM\JoinColumn(name: 'quote_id', referencedColumnName: 'quote_id', nullable: true)]
	private ?Quote $quote;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'sales_person_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $salesPerson;

	#[ORM\Column(name: 'archived_project_file', type: 'text', nullable: true)]
	private ?string $archivedProjectFile;

	#[ORM\Column(name: 'archived_project_file_password', type: 'string', nullable: true)]
	private ?string $archivedProjectFilePassword;

	#[ORM\Column(name: 'created_on', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdOn;

	#[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'projects')]
	#[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'service_id', nullable: true)]
	private ?Service $service;

	#[ORM\Column(name: 'origin', type: 'text', nullable: true)]
	private ?string $origin;

	#[ORM\Column(name: 'volume', type: 'decimal', precision: 19, scale: 3, nullable: true)]
	private ?float $volume;

	#[ORM\Column(name: 'budget_code', type: 'text', nullable: true)]
	private ?string $budgetCode;

	#[ORM\Column(name: 'requested_as_key', type: 'text', nullable: true)]
	private ?string $requestedAsKey;

	#[ORM\Column(name: 'archived_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $archivedAt;

	#[ORM\Column(name: 'assisted_project_id', type: 'string', nullable: true)]
	private ?string $assistedProjectId;

	#[ORM\Column(name: 'date_of_event', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateOfEvent;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\Column(name: 'account_manager_deadline_reminder_id', type: 'bigint', nullable: true)]
	private ?string $accountManagerDeadlineReminderId;

	#[ORM\Column(name: 'project_coordinator_deadline_reminder_id', type: 'bigint', nullable: true)]
	private ?string $projectCoordinatorDeadlineReminderId;

	#[ORM\Column(name: 'project_manager_deadline_reminder_id', type: 'bigint', nullable: true)]
	private ?string $projectManagerDeadlineReminderId;

	#[ORM\Column(name: 'link_parent_project_id', type: 'text', nullable: true)]
	private ?string $linkParentProjectId;

	#[ORM\Column(name: 'confirmation_sent_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $confirmationSentDate;

	#[ORM\Column(name: 'created_by_user_signed_in_as_partner_id', type: 'bigint', nullable: true)]
	private ?string $createdByUserSignedInAsPartnerId;

	#[ORM\Column(name: 'total_activities', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $totalActivities;

	#[ORM\Column(name: 'progress_activities', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $progressActivities;

	#[ORM\Column(name: 'chat_enabled', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $chatEnabled;

	#[ORM\Column(name: 'total_agreed', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $totalAgreed;

	#[ORM\Column(name: 'total_cost', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $totalCost;

	#[ORM\Column(name: 'margin', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $margin;

	#[ORM\Column(name: 'time_based_cost', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $timeBasedCost;

	#[ORM\Column(name: 'tm_savings', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $tmSavings;

	#[ORM\Column(name: 'rentability', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $rentability;

	#[ORM\Column(name: 'minimum', type: 'boolean', nullable: true, options: ['default' => 'false'])]
	private ?float $minimum;

	#[ORM\Column(name: 'audience', type: 'string', length: 1000, nullable: true)]
	private ?string $audience;

	#[ORM\Column(name: 'billing_contact', type: 'string', length: 1000, nullable: true)]
	private ?string $billingContact;

	#[ORM\Column(name: 'cost_center', type: 'string', length: 1000, nullable: true)]
	private ?string $costCenter;

	#[ORM\Column(name: 'domain', type: 'string', length: 1000, nullable: true)]
	private ?string $domain;

	#[ORM\Column(name: 'function', type: 'string', length: 1000, nullable: true)]
	private ?string $function;

	#[ORM\Column(name: 'genre', type: 'string', length: 1000, nullable: true)]
	private ?string $genre;

	#[ORM\Column(name: 'invoice_address', type: 'string', length: 1000, nullable: true)]
	private ?string $invoiceAddress;

	#[ORM\Column(name: 'invoice_notes', type: 'boolean', nullable: true)]
	private ?bool $invoiceNotes;

	#[ORM\Column(name: 'li_provider_name', type: 'text', nullable: true)]
	private ?string $liProviderName;

	#[ORM\Column(name: 'nuid', type: 'string', length: 1000, nullable: true)]
	private ?string $nuid;

	#[ORM\Column(name: 'otn_number', type: 'string', length: 1000, nullable: true)]
	private ?string $otnNumber;

	#[ORM\Column(name: 'pr_acc_status', type: 'string', length: 1000, nullable: true)]
	private ?string $prAccStatus;

	#[ORM\Column(name: 'purpose', type: 'string', length: 2000, nullable: true)]
	private ?string $purpose;

	#[ORM\Column(name: 'rapid_fire', type: 'string', nullable: true)]
	private ?string $rapidFire;

	#[ORM\Column(name: 'rush', type: 'boolean', nullable: true)]
	private ?bool $rush;

	#[ORM\Column(name: 'send_source', type: 'boolean', nullable: true)]
	private ?bool $sendSource;

	#[ORM\Column(name: 'source', type: 'string', length: 1000, nullable: true)]
	private ?string $source;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $workflow;

	#[ORM\OneToMany(targetEntity: ProjectLanguageCombination::class, mappedBy: 'project')]
	private mixed $languagesCombinations;

	#[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project', cascade: ['persist'])]
	private mixed $tasks;

	#[ORM\OneToMany(targetEntity: AnalyticsProject::class, mappedBy: 'project', cascade: ['detach'])]
	private mixed $analyticsProjects;

	#[ORM\JoinTable(name: 'project_categories')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'project_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'projects')]
	protected mixed $categories;

	#[ORM\JoinTable(name: 'project_additional_contact_persons')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'person_id', referencedColumnName: 'contact_person_id')]
	#[ORM\ManyToMany(targetEntity: CustomerPerson::class, cascade: ['persist'], inversedBy: 'projects')]
	protected mixed $customerPersons;

	#[ORM\OneToMany(targetEntity: Feedback::class, mappedBy: 'relatedProject', cascade: ['persist'])]
	private mixed $feedbacks;

	public function __construct()
	{
		$this->tasks = new ArrayCollection();
		$this->analyticsProjects = new ArrayCollection();
		$this->categories = new ArrayCollection();
		$this->customerPersons = new ArrayCollection();
		$this->languagesCombinations = new ArrayCollection();
		$this->feedbacks = new ArrayCollection();
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

	public function getCustomerProjectNumber(): ?string
	{
		return $this->customerProjectNumber;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerProjectNumber(?string $customerProjectNumber): self
	{
		$this->customerProjectNumber = $customerProjectNumber;

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

	public function getIdNumber(): ?string
	{
		return $this->idNumber;
	}

	/**
	 * @return mixed
	 */
	public function setIdNumber(string $idNumber): self
	{
		$this->idNumber = $idNumber;

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

	public function getOrderConfirmationRecipient(): ?string
	{
		return $this->orderConfirmationRecipient;
	}

	/**
	 * @return mixed
	 */
	public function setOrderConfirmationRecipient(?string $orderConfirmationRecipient): self
	{
		$this->orderConfirmationRecipient = $orderConfirmationRecipient;

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

	public function getDeliveryMethod(): ?string
	{
		return $this->deliveryMethod;
	}

	/**
	 * @return mixed
	 */
	public function setDeliveryMethod(?string $deliveryMethod): self
	{
		$this->deliveryMethod = $deliveryMethod;

		return $this;
	}

	public function getDeliverySettings(): ?string
	{
		return $this->deliverySettings;
	}

	/**
	 * @return mixed
	 */
	public function setDeliverySettings(?string $deliverySettings): self
	{
		$this->deliverySettings = $deliverySettings;

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

	public function getSurveyComment(): ?string
	{
		return $this->surveyComment;
	}

	/**
	 * @return mixed
	 */
	public function setSurveyComment(?string $surveyComment): self
	{
		$this->surveyComment = $surveyComment;

		return $this;
	}

	public function getSurveyRequestDateSent(): ?\DateTimeInterface
	{
		return $this->surveyRequestDateSent;
	}

	/**
	 * @return mixed
	 */
	public function setSurveyRequestDateSent(?\DateTimeInterface $surveyRequestDateSent): self
	{
		$this->surveyRequestDateSent = $surveyRequestDateSent;

		return $this;
	}

	public function getSurveySent(): ?bool
	{
		return $this->surveySent;
	}

	/**
	 * @return mixed
	 */
	public function setSurveySent(?bool $surveySent): self
	{
		$this->surveySent = $surveySent;

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

	public function getSentDate(): ?\DateTimeInterface
	{
		return $this->sentDate;
	}

	/**
	 * @return mixed
	 */
	public function setSentDate(?\DateTimeInterface $sentDate): self
	{
		$this->sentDate = $sentDate;

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

	public function getArchivedProjectFile(): ?string
	{
		return $this->archivedProjectFile;
	}

	/**
	 * @return mixed
	 */
	public function setArchivedProjectFile(?string $archivedProjectFile): self
	{
		$this->archivedProjectFile = $archivedProjectFile;

		return $this;
	}

	public function getArchivedProjectFilePassword(): ?string
	{
		return $this->archivedProjectFilePassword;
	}

	/**
	 * @return mixed
	 */
	public function setArchivedProjectFilePassword(?string $archivedProjectFilePassword): self
	{
		$this->archivedProjectFilePassword = $archivedProjectFilePassword;

		return $this;
	}

	public function getCreatedOn(): ?\DateTimeInterface
	{
		return $this->createdOn;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedOn(?\DateTimeInterface $createdOn): self
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	public function getOrigin(): ?string
	{
		return $this->origin;
	}

	/**
	 * @return mixed
	 */
	public function setOrigin(?string $origin): self
	{
		$this->origin = $origin;

		return $this;
	}

	public function getVolume(): ?string
	{
		return $this->volume;
	}

	/**
	 * @return mixed
	 */
	public function setVolume(?string $volume): self
	{
		$this->volume = $volume;

		return $this;
	}

	public function getBudgetCode(): ?string
	{
		return $this->budgetCode;
	}

	/**
	 * @return mixed
	 */
	public function setBudgetCode(?string $budgetCode): self
	{
		$this->budgetCode = $budgetCode;

		return $this;
	}

	public function getRequestedAsKey(): ?string
	{
		return $this->requestedAsKey;
	}

	/**
	 * @return mixed
	 */
	public function setRequestedAsKey(?string $requestedAsKey): self
	{
		$this->requestedAsKey = $requestedAsKey;

		return $this;
	}

	public function getArchivedAt(): ?\DateTimeInterface
	{
		return $this->archivedAt;
	}

	/**
	 * @return mixed
	 */
	public function setArchivedAt(?\DateTimeInterface $archivedAt): self
	{
		$this->archivedAt = $archivedAt;

		return $this;
	}

	public function getAssistedProjectId(): ?string
	{
		return $this->assistedProjectId;
	}

	/**
	 * @return mixed
	 */
	public function setAssistedProjectId(?string $assistedProjectId): self
	{
		$this->assistedProjectId = $assistedProjectId;

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

	public function getAccountManagerDeadlineReminderId(): ?string
	{
		return $this->accountManagerDeadlineReminderId;
	}

	/**
	 * @return mixed
	 */
	public function setAccountManagerDeadlineReminderId(?string $accountManagerDeadlineReminderId): self
	{
		$this->accountManagerDeadlineReminderId = $accountManagerDeadlineReminderId;

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

	public function getLinkParentProjectId(): ?string
	{
		return $this->linkParentProjectId;
	}

	/**
	 * @return mixed
	 */
	public function setLinkParentProjectId(?string $linkParentProjectId): self
	{
		$this->linkParentProjectId = $linkParentProjectId;

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

	public function getMargin(): ?float
	{
		return $this->margin;
	}

	/**
	 * @return mixed
	 */
	public function setMargin(?float $margin): self
	{
		$this->margin = $margin;

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

	public function getAudience(): ?string
	{
		return $this->audience;
	}

	/**
	 * @return mixed
	 */
	public function setAudience(?string $audience): self
	{
		$this->audience = $audience;

		return $this;
	}

	public function getBillingContact(): ?string
	{
		return $this->billingContact;
	}

	/**
	 * @return mixed
	 */
	public function setBillingContact(?string $billingContact): self
	{
		$this->billingContact = $billingContact;

		return $this;
	}

	public function getCostCenter(): ?string
	{
		return $this->costCenter;
	}

	/**
	 * @return mixed
	 */
	public function setCostCenter(?string $costCenter): self
	{
		$this->costCenter = $costCenter;

		return $this;
	}

	public function getDomain(): ?string
	{
		return $this->domain;
	}

	/**
	 * @return mixed
	 */
	public function setDomain(?string $domain): self
	{
		$this->domain = $domain;

		return $this;
	}

	public function getFunction(): ?string
	{
		return $this->function;
	}

	/**
	 * @return mixed
	 */
	public function setFunction(?string $function): self
	{
		$this->function = $function;

		return $this;
	}

	public function getGenre(): ?string
	{
		return $this->genre;
	}

	/**
	 * @return mixed
	 */
	public function setGenre(?string $genre): self
	{
		$this->genre = $genre;

		return $this;
	}

	public function getInvoiceAddress(): ?string
	{
		return $this->invoiceAddress;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceAddress(?string $invoiceAddress): self
	{
		$this->invoiceAddress = $invoiceAddress;

		return $this;
	}

	public function getInvoiceNotes(): ?bool
	{
		return $this->invoiceNotes;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceNotes(?bool $invoiceNotes): self
	{
		$this->invoiceNotes = $invoiceNotes;

		return $this;
	}

	public function getLiProviderName(): ?string
	{
		return $this->liProviderName;
	}

	/**
	 * @return mixed
	 */
	public function setLiProviderName(?string $liProviderName): self
	{
		$this->liProviderName = $liProviderName;

		return $this;
	}

	public function getNuid(): ?string
	{
		return $this->nuid;
	}

	/**
	 * @return mixed
	 */
	public function setNuid(?string $nuid): self
	{
		$this->nuid = $nuid;

		return $this;
	}

	public function getOtnNumber(): ?string
	{
		return $this->otnNumber;
	}

	/**
	 * @return mixed
	 */
	public function setOtnNumber(?string $otnNumber): self
	{
		$this->otnNumber = $otnNumber;

		return $this;
	}

	public function getPrAccStatus(): ?string
	{
		return $this->prAccStatus;
	}

	/**
	 * @return mixed
	 */
	public function setPrAccStatus(?string $prAccStatus): self
	{
		$this->prAccStatus = $prAccStatus;

		return $this;
	}

	public function getPurpose(): ?string
	{
		return $this->purpose;
	}

	/**
	 * @return mixed
	 */
	public function setPurpose(?string $purpose): self
	{
		$this->purpose = $purpose;

		return $this;
	}

	public function getRapidFire(): ?string
	{
		return $this->rapidFire;
	}

	/**
	 * @return mixed
	 */
	public function setRapidFire(?string $rapidFire): self
	{
		$this->rapidFire = $rapidFire;

		return $this;
	}

	public function getSource(): ?string
	{
		return $this->source;
	}

	/**
	 * @return mixed
	 */
	public function setSource(?string $source): self
	{
		$this->source = $source;

		return $this;
	}

	public function getRush(): ?bool
	{
		return $this->rush;
	}

	/**
	 * @return mixed
	 */
	public function setRush(?bool $rush): self
	{
		$this->rush = $rush;

		return $this;
	}

	public function getSendSource(): ?bool
	{
		return $this->sendSource;
	}

	/**
	 * @return mixed
	 */
	public function setSendSource(?bool $sendSource): self
	{
		$this->sendSource = $sendSource;

		return $this;
	}

	public function getAccountManager(): ?User
	{
		return $this->accountManager;
	}

	/**
	 * @return mixed
	 */
	public function setAccountManager(?User $accountManager): self
	{
		$this->accountManager = $accountManager;

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

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	/**
	 * @return mixed
	 */
	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getCustomerPriceProfile(): ?CustomerPriceProfile
	{
		return $this->customerPriceProfile;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerPriceProfile(?CustomerPriceProfile $customerPriceProfile): self
	{
		$this->customerPriceProfile = $customerPriceProfile;

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

	public function getSendBackToContact(): ?CustomerPerson
	{
		return $this->sendBackToContact;
	}

	/**
	 * @return mixed
	 */
	public function setSendBackToContact(?CustomerPerson $sendBackToContact): self
	{
		$this->sendBackToContact = $sendBackToContact;

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

	public function getSalesPerson(): ?User
	{
		return $this->salesPerson;
	}

	/**
	 * @return mixed
	 */
	public function setSalesPerson(?User $salesPerson): self
	{
		$this->salesPerson = $salesPerson;

		return $this;
	}

	public function getService(): ?Service
	{
		return $this->service;
	}

	/**
	 * @return mixed
	 */
	public function setService(?Service $service): self
	{
		$this->service = $service;

		return $this;
	}

	public function getTasks(): Collection
	{
		return $this->tasks;
	}

	/**
	 * @return mixed
	 */
	public function addTask(Task $task): self
	{
		if (!$this->tasks->contains($task)) {
			$this->tasks[] = $task;
			$task->setProject($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeTask(Task $task): self
	{
		if ($this->tasks->contains($task)) {
			$this->tasks->removeElement($task);
			// set the owning side to null (unless already changed)
			if ($task->getProject() === $this) {
				$task->setProject(null);
			}
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
			$analyticsProject->setProject($this);
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
			if ($analyticsProject->getProject() === $this) {
				$analyticsProject->setProject(null);
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

	public function getCustomerPersons(): Collection
	{
		return $this->customerPersons;
	}

	/**
	 * @return mixed
	 */
	public function addCustomerPerson(CustomerPerson $customerPerson): self
	{
		if (!$this->customerPersons->contains($customerPerson)) {
			$this->customerPersons[] = $customerPerson;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomerPerson(CustomerPerson $customerPerson): self
	{
		if ($this->customerPersons->contains($customerPerson)) {
			$this->customerPersons->removeElement($customerPerson);
		}

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

	public function getConfirmationSentDate(): ?\DateTimeInterface
	{
		return $this->confirmationSentDate;
	}

	/**
	 * @return mixed
	 */
	public function setConfirmationSentDate(?\DateTimeInterface $confirmationSentDate): self
	{
		$this->confirmationSentDate = $confirmationSentDate;

		return $this;
	}

	public function getCreatedByUserSignedInAsPartnerId(): ?string
	{
		return $this->createdByUserSignedInAsPartnerId;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedByUserSignedInAsPartnerId(?string $createdByUserSignedInAsPartnerId): self
	{
		$this->createdByUserSignedInAsPartnerId = $createdByUserSignedInAsPartnerId;

		return $this;
	}

	public function getLanguagesCombinations(): Collection
	{
		return $this->languagesCombinations;
	}

	/**
	 * @return mixed
	 */
	public function addLanguagesCombination(ProjectLanguageCombination $languagesCombination): self
	{
		if (!$this->languagesCombinations->contains($languagesCombination)) {
			$this->languagesCombinations[] = $languagesCombination;
			$languagesCombination->setProject($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLanguagesCombination(ProjectLanguageCombination $languagesCombination): self
	{
		if ($this->languagesCombinations->contains($languagesCombination)) {
			$this->languagesCombinations->removeElement($languagesCombination);
			// set the owning side to null (unless already changed)
			if ($languagesCombination->getProject() === $this) {
				$languagesCombination->setProject(null);
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

	public function getChatEnabled(): ?bool
	{
		return $this->chatEnabled;
	}

	/**
	 * @return mixed
	 */
	public function setChatEnabled(bool $chatEnabled): self
	{
		$this->chatEnabled = $chatEnabled;

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
		if (!$this->tasks->contains($feedback)) {
			$this->feedbacks[] = $feedback;
			$feedback->setRelatedProject($this);
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
			// set the owning side to null (unless already changed)
			if ($feedback->getRelatedProject() === $this) {
				$feedback->setRelatedProject(null);
			}
		}

		return $this;
	}

	public function isQuickNote(): ?bool
	{
		return $this->quickNote;
	}

	public function isSurveySent(): ?bool
	{
		return $this->surveySent;
	}

	public function isChatEnabled(): ?bool
	{
		return $this->chatEnabled;
	}

	public function isMinimum(): ?bool
	{
		return $this->minimum;
	}

	public function isInvoiceNotes(): ?bool
	{
		return $this->invoiceNotes;
	}

	public function isRush(): ?bool
	{
		return $this->rush;
	}

	public function isSendSource(): ?bool
	{
		return $this->sendSource;
	}

	public function getWorkflow(): ?Workflow
	{
		return $this->workflow;
	}

	public function setWorkflow(?Workflow $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}
}
