<?php

namespace App\Model\Entity;

use App\Model\Repository\QuoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'quote')]
#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote implements EntityInterface
{
	public const STATUS_ACCEPTED = 'ACCEPTED';
	public const STATUS_ACCEPTED_BY_CLIENT = 'ACCEPTED_BY_CUSTOMER';
	public const STATUS_PENDING = 'PENDING';
	public const STATUS_REJECTED = 'REJECTED';
	public const STATUS_REQUESTED = 'REQUESTED';
	public const STATUS_SENT = 'SENT';
	public const STATUS_SPLIT = 'SPLIT';

	public const ACTION_ACCEPT = 'ACCEPT';
	public const ACTION_REJECT = 'REJECT';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'quote_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'quote_id', type: 'bigint')]
	private string $id;

	/**
	 * @var \DateTime|null
	 */
	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	/**
	 * @var \DateTime|null
	 */
	#[ORM\Column(name: 'date_of_event', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateOfEvent;

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
	private ?string $orderConfirmationRecipientPersonType;

	#[ORM\Column(name: 'payment_note', type: 'text', nullable: true)]
	private ?string $paymentNote;

	#[ORM\Column(name: 'place', type: 'string', nullable: true)]
	private ?string $place;

	#[ORM\Column(name: 'project_delivery_method', type: 'string', nullable: true)]
	private ?string $projectDeliveryMethod;

	#[ORM\Column(name: 'project_delivery_settings', type: 'text', nullable: true)]
	private ?string $projectDeliverySettings;

	#[ORM\Column(name: 'provider_special_instructions', type: 'text', nullable: true)]
	private ?string $providerSpecialInstructions;

	#[ORM\Column(name: 'quick_note', type: 'boolean', nullable: false)]
	private bool $quickNote;

	#[ORM\Column(name: 'accepter_compound_id', type: 'string', nullable: true)]
	private ?string $accepterCompoundId;

	#[ORM\Column(name: 'auto_accept_sent_quote', type: 'boolean', nullable: false)]
	private bool $autoAcceptSentQuote;

	#[ORM\Column(name: 'deadline', type: 'datetime', nullable: true)]
	private ?\DateTime $deadline;

	#[ORM\Column(name: 'start_date', type: 'datetime', nullable: false)]
	private \DateTime $startDate;

	#[ORM\Column(name: 'estimated_delivery_date', type: 'datetime', nullable: true)]
	private ?\DateTime $estimatedDeliveryDate;

	#[ORM\Column(name: 'has_associated_offer', type: 'boolean', nullable: true)]
	private ?bool $hasAssociatedOffer;

	#[ORM\Column(name: 'offer_expiry', type: 'datetime', nullable: true)]
	private ?\DateTime $offerExpiry;

	#[ORM\Column(name: 'quote_start_date', type: 'datetime', nullable: true)]
	private ?\DateTime $quoteStartDate;

	#[ORM\Column(name: 'rejection_reason', type: 'text', nullable: true)]
	private ?string $rejectionReason;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\Column(name: 'working_days', type: 'string', nullable: true)]
	private ?string $workingDays;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'account_manager_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $accountManager;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'customer_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $customerContactPerson;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
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
	private ?string $template;

	#[ORM\Column(name: 'account_manager_expiry_reminder_id', type: 'bigint', nullable: true)]
	private ?string $accountManagerExpiryReminderId;

	#[ORM\Column(name: 'sales_person_expiry_reminder_id', type: 'bigint', nullable: true)]
	private ?string $salesPersonExpiryReminderId;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $workflow;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'sales_person_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $salesPerson;

	#[ORM\ManyToOne(targetEntity: Service::class)]
	#[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'service_id', nullable: true)]
	private ?Service $service;

	#[ORM\Column(name: 'link_parent_project_id', type: 'text', nullable: true)]
	private ?string $linkParentProjectId;

	#[ORM\Column(name: 'assisted_project_id', type: 'string', nullable: true)]
	private ?string $assistedProjectId;

	#[ORM\Column(name: 'origin', type: 'text', nullable: true)]
	private ?string $origin;

	#[ORM\ManyToOne(targetEntity: RejectionReason::class)]
	#[ORM\JoinColumn(name: 'rejection_reason_id', referencedColumnName: 'rejection_reason_id', nullable: true)]
	private ?RejectionReason $rejectionReasonId;

	#[ORM\Column(name: 'volume', type: 'decimal', precision: 19, scale: 3, nullable: true)]
	private ?float $volume;

	#[ORM\Column(name: 'budget_code', type: 'text', nullable: true)]
	private ?string $budgetCode;

	#[ORM\Column(name: 'requested_as_key', type: 'text', nullable: true)]
	private ?string $requestedAsKey;

	#[ORM\Column(name: 'archived_quote_file', type: 'text', nullable: true)]
	private ?string $archivedQuoteFile;

	#[ORM\Column(name: 'archived_quote_file_password', type: 'string', nullable: true)]
	private ?string $archivedQuoteFilePassword;

	#[ORM\Column(name: 'archived_at', type: 'datetime', nullable: true)]
	private ?\DateTime $archivedAt;

	#[ORM\Column(name: 'created_by_user_signed_in_as_partner_id', type: 'bigint', nullable: true)]
	private ?string $createdByUserSignedInAsPartnerId;

	#[ORM\Column(name: 'sent_date', type: 'datetime', nullable: true)]
	private ?\DateTime $sentDate;

	// ##########CUSTOM FIELDS START HERE ##################################

	#[ORM\Column(name: 'audience', type: 'string', length: 1000, nullable: true)]
	private ?string $audience;

	#[ORM\Column(name: 'domain', type: 'string', length: 1000, nullable: true)]
	private ?string $domain;

	#[ORM\Column(name: 'function', type: 'string', length: 1000, nullable: true)]
	private ?string $function;

	#[ORM\Column(name: 'genre', type: 'string', length: 1000, nullable: true)]
	private ?string $genre;

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

	#[ORM\Column(name: 'invoice_address', type: 'string', length: 1000, nullable: true)]
	private ?string $invoiceAddress;

	#[ORM\Column(name: 'invoice_notes', type: 'boolean', nullable: true)]
	private ?bool $invoiceNotes;

	#[ORM\Column(name: 'li_provider_name', type: 'text', nullable: true)]
	private ?string $liProviderName;

	#[ORM\Column(name: 'quote_address', type: 'text', nullable: true)]
	private ?string $quoteAddress;

	// ############ EXTERNAL FIELDS START HERE################

	#[ORM\Column(name: 'margin', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $margin;

	#[ORM\Column(name: 'time_based_cost', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $timeBasedCost;

	#[ORM\Column(name: 'tm_savings', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $tmSavings;

	#[ORM\Column(name: 'converted', type: 'boolean', options: ['default' => 'false'])]
	private bool $converted;

	#[ORM\Column(name: 'total_agreed', type: 'decimal', precision: 19, scale: 6, nullable: true, options: ['default' => 0])]
	private ?float $totalAgreed;

	#[ORM\Column(name: 'total_cost', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $totalCost;

	#[ORM\Column(name: 'rentability', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $rentability;

	#[ORM\Column(name: 'chat_enabled', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $chatEnabled;

	#[ORM\Column(name: 'cost_center', type: 'string', length: 1000, nullable: true)]
	private ?string $costCenter;

	#[ORM\Column(name: 'nuid', type: 'string', length: 1000, nullable: true)]
	private ?string $nuid;

	#[ORM\Column(name: 'billing_contact', type: 'string', length: 1000, nullable: true)]
	private ?string $billingContact;

	#[ORM\Column(name: 'otn_number', type: 'string', length: 1000, nullable: true)]
	private ?string $otnNumber;

	#[ORM\Column(name: 'pr_acc_status', type: 'string', length: 1000, nullable: true)]
	private ?string $prAccStatus;

	// ############ NORMAL RELATION FIELDS START HERE################
	#[ORM\JoinTable(name: 'quote_categories')]
	#[ORM\JoinColumn(name: 'quote_id', referencedColumnName: 'quote_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'project_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'quotes', cascade: ['persist'])]
	protected mixed $categories;

	#[ORM\JoinTable(name: 'quote_additional_contact_persons')]
	#[ORM\JoinColumn(name: 'quote_id', referencedColumnName: 'quote_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'person_id', referencedColumnName: 'contact_person_id')]
	#[ORM\ManyToMany(targetEntity: CustomerPerson::class, inversedBy: 'quotes', cascade: ['persist'])]
	protected mixed $customersPerson;

	#[ORM\OneToOne(mappedBy: 'quote', targetEntity: Project::class, cascade: ['persist'])]
	private ?Project $project;

	#[ORM\OneToMany(mappedBy: 'quote', targetEntity: Task::class, cascade: ['persist'])]
	private mixed $tasks;

	#[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteLanguageCombination::class, cascade: ['persist'])]
	private mixed $languagesCombinations;

	public function __construct()
	{
		$this->categories = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->customersPerson = new ArrayCollection();
		$this->languagesCombinations = new ArrayCollection();
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

	public function getProjectDeliveryMethod(): ?string
	{
		return $this->projectDeliveryMethod;
	}

	/**
	 * @return mixed
	 */
	public function setProjectDeliveryMethod(?string $projectDeliveryMethod): self
	{
		$this->projectDeliveryMethod = $projectDeliveryMethod;

		return $this;
	}

	public function getProjectDeliverySettings(): ?string
	{
		return $this->projectDeliverySettings;
	}

	/**
	 * @return mixed
	 */
	public function setProjectDeliverySettings(?string $projectDeliverySettings): self
	{
		$this->projectDeliverySettings = $projectDeliverySettings;

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

	public function getAccepterCompoundId(): ?string
	{
		return $this->accepterCompoundId;
	}

	/**
	 * @return mixed
	 */
	public function setAccepterCompoundId(?string $accepterCompoundId): self
	{
		$this->accepterCompoundId = $accepterCompoundId;

		return $this;
	}

	public function getAutoAcceptSentQuote(): ?bool
	{
		return $this->autoAcceptSentQuote;
	}

	/**
	 * @return mixed
	 */
	public function setAutoAcceptSentQuote(bool $autoAcceptSentQuote): self
	{
		$this->autoAcceptSentQuote = $autoAcceptSentQuote;

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

	public function getHasAssociatedOffer(): ?bool
	{
		return $this->hasAssociatedOffer;
	}

	/**
	 * @return mixed
	 */
	public function setHasAssociatedOffer(?bool $hasAssociatedOffer): self
	{
		$this->hasAssociatedOffer = $hasAssociatedOffer;

		return $this;
	}

	public function getOfferExpiry(): ?\DateTimeInterface
	{
		return $this->offerExpiry;
	}

	/**
	 * @return mixed
	 */
	public function setOfferExpiry(?\DateTimeInterface $offerExpiry): self
	{
		$this->offerExpiry = $offerExpiry;

		return $this;
	}

	public function getQuoteStartDate(): ?\DateTimeInterface
	{
		return $this->quoteStartDate;
	}

	/**
	 * @return mixed
	 */
	public function setQuoteStartDate(?\DateTimeInterface $quoteStartDate): self
	{
		$this->quoteStartDate = $quoteStartDate;

		return $this;
	}

	public function getRejectionReason(): ?string
	{
		return $this->rejectionReason;
	}

	/**
	 * @return mixed
	 */
	public function setRejectionReason(?string $rejectionReason): self
	{
		$this->rejectionReason = $rejectionReason;

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

	public function getWorkingDays(): ?string
	{
		return $this->workingDays;
	}

	/**
	 * @return mixed
	 */
	public function setWorkingDays(?string $workingDays): self
	{
		$this->workingDays = $workingDays;

		return $this;
	}

	public function getTemplate(): ?string
	{
		return $this->template;
	}

	/**
	 * @return mixed
	 */
	public function setTemplate(?string $template): self
	{
		$this->template = $template;

		return $this;
	}

	public function getAccountManagerExpiryReminderId(): ?string
	{
		return $this->accountManagerExpiryReminderId;
	}

	/**
	 * @return mixed
	 */
	public function setAccountManagerExpiryReminderId(?string $accountManagerExpiryReminderId): self
	{
		$this->accountManagerExpiryReminderId = $accountManagerExpiryReminderId;

		return $this;
	}

	public function getSalesPersonExpiryReminderId(): ?string
	{
		return $this->salesPersonExpiryReminderId;
	}

	/**
	 * @return mixed
	 */
	public function setSalesPersonExpiryReminderId(?string $salesPersonExpiryReminderId): self
	{
		$this->salesPersonExpiryReminderId = $salesPersonExpiryReminderId;

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

	public function getArchivedQuoteFile(): ?string
	{
		return $this->archivedQuoteFile;
	}

	/**
	 * @return mixed
	 */
	public function setArchivedQuoteFile(?string $archivedQuoteFile): self
	{
		$this->archivedQuoteFile = $archivedQuoteFile;

		return $this;
	}

	public function getArchivedQuoteFilePassword(): ?string
	{
		return $this->archivedQuoteFilePassword;
	}

	/**
	 * @return mixed
	 */
	public function setArchivedQuoteFilePassword(?string $archivedQuoteFilePassword): self
	{
		$this->archivedQuoteFilePassword = $archivedQuoteFilePassword;

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

	public function getQuoteAddress(): ?string
	{
		return $this->quoteAddress;
	}

	/**
	 * @return mixed
	 */
	public function setQuoteAddress(?string $quoteAddress): self
	{
		$this->quoteAddress = $quoteAddress;

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

	public function getConverted(): ?bool
	{
		return $this->converted;
	}

	/**
	 * @return mixed
	 */
	public function setConverted(bool $converted): self
	{
		$this->converted = $converted;

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

	public function isSendSource(): ?bool
	{
		return $this->sendSource;
	}

	public function setSendSource(bool $sendSource): void
	{
		$this->sendSource = $sendSource;
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

	public function getRejectionReasonId(): ?RejectionReason
	{
		return $this->rejectionReasonId;
	}

	/**
	 * @return mixed
	 */
	public function setRejectionReasonId(?RejectionReason $rejectionReasonId): self
	{
		$this->rejectionReasonId = $rejectionReasonId;

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

	public function getCustomersPerson(): Collection
	{
		return $this->customersPerson;
	}

	/**
	 * @return mixed
	 */
	public function addCustomersPerson(CustomerPerson $customersPerson): self
	{
		if (!$this->customersPerson->contains($customersPerson)) {
			$this->customersPerson[] = $customersPerson;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomersPerson(CustomerPerson $customersPerson): self
	{
		if ($this->customersPerson->contains($customersPerson)) {
			$this->customersPerson->removeElement($customersPerson);
		}

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

		// set (or unset) the owning side of the relation if necessary
		$newQuote = null === $project ? null : $this;
		if ($project->getQuote() !== $newQuote) {
			$project->setQuote($newQuote);
		}

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
			$task->setQuote($this);
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
			if ($task->getQuote() === $this) {
				$task->setQuote(null);
			}
		}

		return $this;
	}

	public function getLanguagesCombinations(): Collection
	{
		return $this->languagesCombinations;
	}

	/**
	 * @return mixed
	 */
	public function addLanguagesCombination(QuoteLanguageCombination $languagesCombination): self
	{
		if (!$this->languagesCombinations->contains($languagesCombination)) {
			$this->languagesCombinations[] = $languagesCombination;
			$languagesCombination->setQuote($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLanguagesCombination(QuoteLanguageCombination $languagesCombination): self
	{
		if ($this->languagesCombinations->contains($languagesCombination)) {
			$this->languagesCombinations->removeElement($languagesCombination);
			// set the owning side to null (unless already changed)
			if ($languagesCombination->getQuote() === $this) {
				$languagesCombination->setQuote(null);
			}
		}

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

	public function getAudience(): ?string
	{
		return $this->audience;
	}

	public function setAudience(?string $audience): self
	{
		$this->audience = $audience;

		return $this;
	}

	public function getDomain(): ?string
	{
		return $this->domain;
	}

	public function setDomain(?string $domain): self
	{
		$this->domain = $domain;

		return $this;
	}

	public function getFunction(): ?string
	{
		return $this->function;
	}

	public function setFunction(?string $function): self
	{
		$this->function = $function;

		return $this;
	}

	public function getGenre(): ?string
	{
		return $this->genre;
	}

	public function setGenre(?string $genre): self
	{
		$this->genre = $genre;

		return $this;
	}

	public function getPurpose(): ?string
	{
		return $this->purpose;
	}

	public function setPurpose(?string $purpose): self
	{
		$this->purpose = $purpose;

		return $this;
	}

	public function getRapidFire(): ?string
	{
		return $this->rapidFire;
	}

	public function setRapidFire(?string $rapidFire): self
	{
		$this->rapidFire = $rapidFire;

		return $this;
	}

	public function getRush(): ?bool
	{
		return $this->rush;
	}

	public function setRush(?bool $rush): self
	{
		$this->rush = $rush;

		return $this;
	}

	public function getInvoiceAddress(): ?string
	{
		return $this->invoiceAddress;
	}

	public function setInvoiceAddress(?string $invoiceAddress): self
	{
		$this->invoiceAddress = $invoiceAddress;

		return $this;
	}

	public function getInvoiceNotes(): ?bool
	{
		return $this->invoiceNotes;
	}

	public function setInvoiceNotes(?bool $invoiceNotes): self
	{
		$this->invoiceNotes = $invoiceNotes;

		return $this;
	}

	public function getOtnNumber(): ?string
	{
		return $this->otnNumber;
	}

	public function setOtnNumber(?string $otnNumber): self
	{
		$this->otnNumber = $otnNumber;

		return $this;
	}

	public function getPrAccStatus(): ?string
	{
		return $this->prAccStatus;
	}

	public function setPrAccStatus(?string $prAccStatus): self
	{
		$this->prAccStatus = $prAccStatus;

		return $this;
	}
}
