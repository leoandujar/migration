<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'customer_invoice')]
#[ORM\UniqueConstraint(name: 'customer_invoice_draft_number_key', columns: ['draft_number'])]
#[ORM\UniqueConstraint(name: 'customer_invoice_final_number_key', columns: ['final_number'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\CustomerInvoiceRepository')]
class CustomerInvoice implements EntityInterface
{
	public const INVOICE_STATUS_READY = 'READY';
	public const INVOICE_STATUS_NO_READY = 'NOT_READY';
	public const INVOICE_STATUS_SENT = 'SENT';

	public const PAYMENT_STATUS_FULL_PAID = 'FULLY_PAID';
	public const PAYMENT_STATUS_PARTIAL_PAID = 'PARTIALLY_PAID';
	public const PAYMENT_STATUS_UNPAID = 'NOT_PAID';
	public const PAYMENT_STATUS_IRRECOVARABLE = 'IRRECOVERABLE';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'customer_invoice_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'draft_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $draftDate;

	#[ORM\Column(name: 'draft_number', type: 'string', nullable: true)]
	private ?string $draftNumber;

	#[ORM\Column(name: 'exchange_ratio_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $exchangeRatioDate;

	#[ORM\Column(name: 'final_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $finalDate;

	#[ORM\Column(name: 'final_number', type: 'string', nullable: true)]
	private ?string $finalNumber;

	#[ORM\Column(name: 'fully_paid_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $fullyPaidDate;

	#[ORM\Column(name: 'internal_note', type: 'text', nullable: true)]
	private ?string $internalNote;

	#[ORM\Column(name: 'invoice_note', type: 'text', nullable: true)]
	private ?string $invoiceNote;

	#[ORM\Column(name: 'invoice_state_changed_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $invoiceStateChangedDate;

	#[ORM\Column(name: 'paid_value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $paidValue;

	#[ORM\Column(name: 'payment_conditions', type: 'string', nullable: true)]
	private ?string $paymentConditions;

	#[ORM\Column(name: 'payment_note', type: 'text', nullable: true)]
	private ?string $paymentNote;

	#[ORM\Column(name: 'payment_state', type: 'string', nullable: false)]
	private string $paymentState;

	#[ORM\Column(name: 'pdf_path', type: 'string', length: 2000, nullable: true)]
	private ?string $pdfPath;

	#[ORM\Column(name: 'required_payment_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $requiredPaymentDate;

	#[ORM\Column(name: 'total_brutto', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $totalBrutto;

	#[ORM\Column(name: 'total_in_words', type: 'string', nullable: true)]
	private ?string $totalInWords;

	#[ORM\Column(name: 'total_netto', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $totalNetto;

	#[ORM\Column(name: 'customer_name', type: 'string', nullable: true)]
	private ?string $customerName;

	#[ORM\Column(name: 'state', type: 'string', nullable: false)]
	private string $state;

	#[ORM\Column(name: 'type', type: 'string', nullable: false)]
	private string $type;

	#[ORM\Column(name: 'tasks_id_numbers', type: 'text', nullable: true)]
	private ?string $tasksIdNumbers;

	#[ORM\Column(name: 'tasks_value', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $tasksValue;

	#[ORM\Column(name: 'vat_calculation_rule', type: 'string', nullable: false)]
	private string $vatCalculationRule;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\Column(name: 'template_id', type: 'bigint', nullable: true)]
	private ?string $template;

	#[ORM\ManyToOne(targetEntity: PaymentCondition::class)]
	#[ORM\JoinColumn(name: 'payment_conditions_id', referencedColumnName: 'payment_conditions_id', nullable: true)]
	private ?PaymentCondition $defaultPaymentConditions;

	#[ORM\Column(name: 'payment_method_id', type: 'bigint', nullable: true)]
	private ?string $paymentMethodId;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'accountency_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $accountencyPerson;

	#[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'invoices')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\ManyToOne(targetEntity: Account::class)]
	#[ORM\JoinColumn(name: 'customer_bank_account_id', referencedColumnName: 'account_id', nullable: true)]
	private ?Account $customerBankAccount;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'signed_person_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $signedPerson;

	#[ORM\Column(name: 'append_currency_to_total_in_words', type: 'boolean', nullable: true)]
	private ?bool $appendCurrencyToTotalInWords;

	#[ORM\Column(name: 'autocalculate_payment_conditions', type: 'boolean', nullable: true)]
	private ?bool $autocalculatePaymentConditions;

	#[ORM\Column(name: 'autocalculate_payment_conditions_description', type: 'boolean', nullable: true)]
	private ?bool $autocalculatePaymentConditionsDescription;

	#[ORM\Column(name: 'backup_old_exchange_ratio_not_used', type: 'decimal', precision: 19, scale: 10, nullable: true)]
	private ?float $backupOldExchangeRatioNotUsed;

	#[ORM\Column(name: 'exchange_ratio_event', type: 'string', nullable: true)]
	private ?string $exchangeRatioEvent;

	#[ORM\Column(name: 'locale', type: 'string', nullable: false)]
	private string $locale;

	#[ORM\Column(name: 'use_converter', type: 'boolean', nullable: true)]
	private ?bool $useConverter;

	#[ORM\Column(name: 'customer_address', type: 'string', nullable: true)]
	private ?string $customerAddress;

	#[ORM\Column(name: 'customer_address_2', type: 'string', nullable: true)]
	private ?string $customerAddress2;

	#[ORM\Column(name: 'customer_city', type: 'string', nullable: true)]
	private ?string $customerCity;

	#[ORM\Column(name: 'customer_dependent_locality', type: 'string', nullable: true)]
	private ?string $customerDependentLocality;

	#[ORM\Column(name: 'customer_sorting_code', type: 'string', nullable: true)]
	private ?string $customerSortingCode;

	#[ORM\Column(name: 'customer_zip_code', type: 'string', nullable: true)]
	private ?string $customerZipCode;

	#[ORM\Column(name: 'customer_fiscal_code', type: 'string', nullable: true)]
	private ?string $customerFiscalCode;

	#[ORM\Column(name: 'customer_vatue', type: 'string', nullable: true)]
	private ?string $customerVatue;

	#[ORM\Column(name: 'draft_number_modified', type: 'boolean', nullable: true)]
	private ?bool $draftNumberModified;

	#[ORM\Column(name: 'final_number_modified', type: 'boolean', nullable: true)]
	private ?bool $finalNumberModified;

	#[ORM\Column(name: 'customer_country_id', type: 'bigint', nullable: true)]
	private ?string $customerCountryId;

	#[ORM\Column(name: 'customer_province_id', type: 'bigint', nullable: true)]
	private ?string $customerProvinceId;

	#[ORM\Column(name: 'numbering_schema_id', type: 'bigint', nullable: true)]
	private ?string $numberingSchemaId;

	#[ORM\Column(name: 'sent_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $sentDate;

	#[ORM\Column(name: 'deposit', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $deposit;

	#[ORM\Column(name: 'balance', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $balance;

	#[ORM\Column(name: 'qbo_id', type: 'string', nullable: true)]
	private ?string $qboId;

	#[ORM\Column(name: 'credit_note_number', type: 'string', nullable: true)]
	private ?string $creditNoteNumber;

	#[ORM\Column(name: 'original_invoice_id', type: 'bigint', nullable: true)]
	private ?string $originalInvoiceId;

	#[ORM\Column(name: 'reason_for_correction_id', type: 'bigint', nullable: true)]
	private ?string $reasonForCorrectionId;

	#[ORM\Column(name: 'invoice_recipient_id', type: 'bigint', nullable: true)]
	private ?string $invoiceRecipientId;

	#[ORM\Column(name: 'credit_note_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $creditNoteDate;

	#[ORM\Column(name: 'credit_note_number_modified', type: 'boolean', nullable: true)]
	private ?bool $creditNoteNumberModified;

	#[ORM\Column(name: 'original_tasks_value', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $originalTasksValue;

	#[ORM\Column(name: 'due_amount', type: 'decimal', precision: 10, scale: 2, nullable: true)]
	private ?float $dueAmount;

	#[ORM\OneToMany(targetEntity: CustomerInvoiceItem::class, mappedBy: 'customerInvoice', orphanRemoval: true)]
	private mixed $customInvoiceItems;

	#[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'customerInvoice')]
	private mixed $tasks;

	#[ORM\OneToMany(targetEntity: CustomerCharge::class, mappedBy: 'customerInvoice')]
	private mixed $customerCharges;

	#[ORM\JoinTable(name: 'customer_invoice_accountency_persons')]
	#[ORM\JoinColumn(name: 'customer_invoice_id', referencedColumnName: 'customer_invoice_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'customer_person_id', referencedColumnName: 'contact_person_id')]
	#[ORM\ManyToMany(targetEntity: CustomerPerson::class, cascade: ['persist'])]
	protected mixed $customersPerson;

	#[ORM\JoinTable(name: 'customer_invoice_categories')]
	#[ORM\JoinColumn(name: 'customer_invoice_id', referencedColumnName: 'customer_invoice_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'customer_invoice_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'customerInvoices')]
	protected mixed $categories;

	public function __construct()
	{
		$this->customInvoiceItems = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->customersPerson = new ArrayCollection();
		$this->categories = new ArrayCollection();
		$this->customerCharges = new ArrayCollection();
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

	public function getDraftDate(): ?\DateTimeInterface
	{
		return $this->draftDate;
	}

	/**
	 * @return mixed
	 */
	public function setDraftDate(?\DateTimeInterface $draftDate): self
	{
		$this->draftDate = $draftDate;

		return $this;
	}

	public function getDraftNumber(): ?string
	{
		return $this->draftNumber;
	}

	/**
	 * @return mixed
	 */
	public function setDraftNumber(?string $draftNumber): self
	{
		$this->draftNumber = $draftNumber;

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

	public function getFinalDate(): ?\DateTimeInterface
	{
		return $this->finalDate;
	}

	/**
	 * @return mixed
	 */
	public function setFinalDate(?\DateTimeInterface $finalDate): self
	{
		$this->finalDate = $finalDate;

		return $this;
	}

	public function getFinalNumber(): ?string
	{
		return $this->finalNumber;
	}

	/**
	 * @return mixed
	 */
	public function setFinalNumber(?string $finalNumber): self
	{
		$this->finalNumber = $finalNumber;

		return $this;
	}

	public function getFullyPaidDate(): ?\DateTimeInterface
	{
		return $this->fullyPaidDate;
	}

	/**
	 * @return mixed
	 */
	public function setFullyPaidDate(?\DateTimeInterface $fullyPaidDate): self
	{
		$this->fullyPaidDate = $fullyPaidDate;

		return $this;
	}

	public function getInternalNote(): ?string
	{
		return $this->internalNote;
	}

	/**
	 * @return mixed
	 */
	public function setInternalNote(?string $internalNote): self
	{
		$this->internalNote = $internalNote;

		return $this;
	}

	public function getInvoiceNote(): ?string
	{
		return $this->invoiceNote;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceNote(?string $invoiceNote): self
	{
		$this->invoiceNote = $invoiceNote;

		return $this;
	}

	public function getInvoiceStateChangedDate(): ?\DateTimeInterface
	{
		return $this->invoiceStateChangedDate;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceStateChangedDate(?\DateTimeInterface $invoiceStateChangedDate): self
	{
		$this->invoiceStateChangedDate = $invoiceStateChangedDate;

		return $this;
	}

	public function getPaidValue(): ?string
	{
		return $this->paidValue;
	}

	/**
	 * @return mixed
	 */
	public function setPaidValue(string $paidValue): self
	{
		$this->paidValue = $paidValue;

		return $this;
	}

	public function getPaymentConditions(): ?string
	{
		return $this->paymentConditions;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentConditions(?string $paymentConditions): self
	{
		$this->paymentConditions = $paymentConditions;

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

	public function getPaymentState(): ?string
	{
		return $this->paymentState;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentState(string $paymentState): self
	{
		$this->paymentState = $paymentState;

		return $this;
	}

	public function getPdfPath(): ?string
	{
		return $this->pdfPath;
	}

	/**
	 * @return mixed
	 */
	public function setPdfPath(?string $pdfPath): self
	{
		$this->pdfPath = $pdfPath;

		return $this;
	}

	public function getRequiredPaymentDate(): ?\DateTimeInterface
	{
		return $this->requiredPaymentDate;
	}

	/**
	 * @return mixed
	 */
	public function setRequiredPaymentDate(?\DateTimeInterface $requiredPaymentDate): self
	{
		$this->requiredPaymentDate = $requiredPaymentDate;

		return $this;
	}

	public function getTotalBrutto(): ?string
	{
		return $this->totalBrutto;
	}

	/**
	 * @return mixed
	 */
	public function setTotalBrutto(string $totalBrutto): self
	{
		$this->totalBrutto = $totalBrutto;

		return $this;
	}

	public function getTotalInWords(): ?string
	{
		return $this->totalInWords;
	}

	/**
	 * @return mixed
	 */
	public function setTotalInWords(?string $totalInWords): self
	{
		$this->totalInWords = $totalInWords;

		return $this;
	}

	public function getTotalNetto(): ?string
	{
		return $this->totalNetto;
	}

	/**
	 * @return mixed
	 */
	public function setTotalNetto(string $totalNetto): self
	{
		$this->totalNetto = $totalNetto;

		return $this;
	}

	public function getCustomerName(): ?string
	{
		return $this->customerName;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerName(?string $customerName): self
	{
		$this->customerName = $customerName;

		return $this;
	}

	public function getState(): ?string
	{
		return $this->state;
	}

	/**
	 * @return mixed
	 */
	public function setState(string $state): self
	{
		$this->state = $state;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getTasksIdNumbers(): ?string
	{
		return $this->tasksIdNumbers;
	}

	/**
	 * @return mixed
	 */
	public function setTasksIdNumbers(?string $tasksIdNumbers): self
	{
		$this->tasksIdNumbers = $tasksIdNumbers;

		return $this;
	}

	public function getTasksValue(): ?string
	{
		return $this->tasksValue;
	}

	/**
	 * @return mixed
	 */
	public function setTasksValue(?string $tasksValue): self
	{
		$this->tasksValue = $tasksValue;

		return $this;
	}

	public function getVatCalculationRule(): ?string
	{
		return $this->vatCalculationRule;
	}

	/**
	 * @return mixed
	 */
	public function setVatCalculationRule(string $vatCalculationRule): self
	{
		$this->vatCalculationRule = $vatCalculationRule;

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

	public function getPaymentMethodId(): ?string
	{
		return $this->paymentMethodId;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentMethodId(?string $paymentMethodId): self
	{
		$this->paymentMethodId = $paymentMethodId;

		return $this;
	}

	public function getAppendCurrencyToTotalInWords(): ?bool
	{
		return $this->appendCurrencyToTotalInWords;
	}

	/**
	 * @return mixed
	 */
	public function setAppendCurrencyToTotalInWords(?bool $appendCurrencyToTotalInWords): self
	{
		$this->appendCurrencyToTotalInWords = $appendCurrencyToTotalInWords;

		return $this;
	}

	public function getAutocalculatePaymentConditions(): ?bool
	{
		return $this->autocalculatePaymentConditions;
	}

	/**
	 * @return mixed
	 */
	public function setAutocalculatePaymentConditions(?bool $autocalculatePaymentConditions): self
	{
		$this->autocalculatePaymentConditions = $autocalculatePaymentConditions;

		return $this;
	}

	public function getAutocalculatePaymentConditionsDescription(): ?bool
	{
		return $this->autocalculatePaymentConditionsDescription;
	}

	/**
	 * @return mixed
	 */
	public function setAutocalculatePaymentConditionsDescription(?bool $autocalculatePaymentConditionsDescription): self
	{
		$this->autocalculatePaymentConditionsDescription = $autocalculatePaymentConditionsDescription;

		return $this;
	}

	public function getBackupOldExchangeRatioNotUsed(): ?string
	{
		return $this->backupOldExchangeRatioNotUsed;
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

	public function getLocale(): ?string
	{
		return $this->locale;
	}

	/**
	 * @return mixed
	 */
	public function setLocale(string $locale): self
	{
		$this->locale = $locale;

		return $this;
	}

	public function getUseConverter(): ?bool
	{
		return $this->useConverter;
	}

	/**
	 * @return mixed
	 */
	public function setUseConverter(?bool $useConverter): self
	{
		$this->useConverter = $useConverter;

		return $this;
	}

	public function getCustomerAddress(): ?string
	{
		return $this->customerAddress;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerAddress(?string $customerAddress): self
	{
		$this->customerAddress = $customerAddress;

		return $this;
	}

	public function getCustomerAddress2(): ?string
	{
		return $this->customerAddress2;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerAddress2(?string $customerAddress2): self
	{
		$this->customerAddress2 = $customerAddress2;

		return $this;
	}

	public function getCustomerCity(): ?string
	{
		return $this->customerCity;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerCity(?string $customerCity): self
	{
		$this->customerCity = $customerCity;

		return $this;
	}

	public function getCustomerDependentLocality(): ?string
	{
		return $this->customerDependentLocality;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerDependentLocality(?string $customerDependentLocality): self
	{
		$this->customerDependentLocality = $customerDependentLocality;

		return $this;
	}

	public function getCustomerSortingCode(): ?string
	{
		return $this->customerSortingCode;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerSortingCode(?string $customerSortingCode): self
	{
		$this->customerSortingCode = $customerSortingCode;

		return $this;
	}

	public function getCustomerZipCode(): ?string
	{
		return $this->customerZipCode;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerZipCode(?string $customerZipCode): self
	{
		$this->customerZipCode = $customerZipCode;

		return $this;
	}

	public function getCustomerFiscalCode(): ?string
	{
		return $this->customerFiscalCode;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerFiscalCode(?string $customerFiscalCode): self
	{
		$this->customerFiscalCode = $customerFiscalCode;

		return $this;
	}

	public function getCustomerVatue(): ?string
	{
		return $this->customerVatue;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerVatue(?string $customerVatue): self
	{
		$this->customerVatue = $customerVatue;

		return $this;
	}

	public function getDraftNumberModified(): ?bool
	{
		return $this->draftNumberModified;
	}

	/**
	 * @return mixed
	 */
	public function setDraftNumberModified(?bool $draftNumberModified): self
	{
		$this->draftNumberModified = $draftNumberModified;

		return $this;
	}

	public function getFinalNumberModified(): ?bool
	{
		return $this->finalNumberModified;
	}

	/**
	 * @return mixed
	 */
	public function setFinalNumberModified(?bool $finalNumberModified): self
	{
		$this->finalNumberModified = $finalNumberModified;

		return $this;
	}

	public function getCustomerCountryId(): ?string
	{
		return $this->customerCountryId;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerCountryId(?string $customerCountryId): self
	{
		$this->customerCountryId = $customerCountryId;

		return $this;
	}

	public function getCustomerProvinceId(): ?string
	{
		return $this->customerProvinceId;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerProvinceId(?string $customerProvinceId): self
	{
		$this->customerProvinceId = $customerProvinceId;

		return $this;
	}

	public function getNumberingSchemaId(): ?string
	{
		return $this->numberingSchemaId;
	}

	/**
	 * @return mixed
	 */
	public function setNumberingSchemaId(?string $numberingSchemaId): self
	{
		$this->numberingSchemaId = $numberingSchemaId;

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

	public function getDueAmount(): ?string
	{
		return $this->dueAmount;
	}

	/**
	 * @return mixed
	 */
	public function setDueAmount(?string $dueAmount): self
	{
		$this->dueAmount = $dueAmount;

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

	public function getDefaultPaymentConditions(): ?PaymentCondition
	{
		return $this->defaultPaymentConditions;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultPaymentConditions(?PaymentCondition $defaultPaymentConditions): self
	{
		$this->defaultPaymentConditions = $defaultPaymentConditions;

		return $this;
	}

	public function getAccountencyPerson(): ?CustomerPerson
	{
		return $this->accountencyPerson;
	}

	/**
	 * @return mixed
	 */
	public function setAccountencyPerson(?CustomerPerson $accountencyPerson): self
	{
		$this->accountencyPerson = $accountencyPerson;

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

	public function getCustomerBankAccount(): ?Account
	{
		return $this->customerBankAccount;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerBankAccount(?Account $customerBankAccount): self
	{
		$this->customerBankAccount = $customerBankAccount;

		return $this;
	}

	public function getSignedPerson(): ?User
	{
		return $this->signedPerson;
	}

	/**
	 * @return mixed
	 */
	public function setSignedPerson(?User $signedPerson): self
	{
		$this->signedPerson = $signedPerson;

		return $this;
	}

	public function getCustomInvoiceItems(): Collection
	{
		return $this->customInvoiceItems;
	}

	/**
	 * @return mixed
	 */
	public function addCustomInvoiceItem(CustomerInvoiceItem $customInvoiceItem): self
	{
		if (!$this->customInvoiceItems->contains($customInvoiceItem)) {
			$this->customInvoiceItems[] = $customInvoiceItem;
			$customInvoiceItem->setCustomerInvoice($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomInvoiceItem(CustomerInvoiceItem $customInvoiceItem): self
	{
		if ($this->customInvoiceItems->contains($customInvoiceItem)) {
			$this->customInvoiceItems->removeElement($customInvoiceItem);
			// set the owning side to null (unless already changed)
			if ($customInvoiceItem->getCustomerInvoice() === $this) {
				$customInvoiceItem->setCustomerInvoice(null);
			}
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
			$task->setCustomerInvoice($this);
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
			if ($task->getCustomerInvoice() === $this) {
				$task->setCustomerInvoice(null);
			}
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

	public function getQboId(): ?string
	{
		return $this->qboId;
	}

	/**
	 * @return mixed
	 */
	public function setQboId(?string $qboId): self
	{
		$this->qboId = $qboId;

		return $this;
	}

	public function getDeposit(): ?string
	{
		return $this->deposit;
	}

	/**
	 * @return mixed
	 */
	public function setDeposit(?string $deposit): self
	{
		$this->deposit = $deposit;

		return $this;
	}

	public function getBalance(): ?string
	{
		return $this->balance;
	}

	/**
	 * @return mixed
	 */
	public function setBalance(?string $balance): self
	{
		$this->balance = $balance;

		return $this;
	}

	public function getCustomerCharges(): Collection
	{
		return $this->customerCharges;
	}

	/**
	 * @return mixed
	 */
	public function addCustomerCharge(CustomerCharge $customerCharge): self
	{
		if (!$this->customerCharges->contains($customerCharge)) {
			$this->customerCharges[] = $customerCharge;
			$customerCharge->setCustomerInvoice($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomerCharge(CustomerCharge $customerCharge): self
	{
		if ($this->customerCharges->contains($customerCharge)) {
			$this->customerCharges->removeElement($customerCharge);
			// set the owning side to null (unless already changed)
			if ($customerCharge->getCustomerInvoice() === $this) {
				$customerCharge->setCustomerInvoice(null);
			}
		}

		return $this;
	}

	public function getCreditNoteNumber(): ?string
	{
		return $this->creditNoteNumber;
	}

	public function setCreditNoteNumber(?string $creditNoteNumber): self
	{
		$this->creditNoteNumber = $creditNoteNumber;

		return $this;
	}

	public function getOriginalInvoiceId(): ?string
	{
		return $this->originalInvoiceId;
	}

	public function setOriginalInvoiceId(?string $originalInvoiceId): self
	{
		$this->originalInvoiceId = $originalInvoiceId;

		return $this;
	}

	public function getReasonForCorrectionId(): ?string
	{
		return $this->reasonForCorrectionId;
	}

	public function setReasonForCorrectionId(?string $reasonForCorrectionId): self
	{
		$this->reasonForCorrectionId = $reasonForCorrectionId;

		return $this;
	}

	public function getInvoiceRecipientId(): ?string
	{
		return $this->invoiceRecipientId;
	}

	public function setInvoiceRecipientId(?string $invoiceRecipientId): self
	{
		$this->invoiceRecipientId = $invoiceRecipientId;

		return $this;
	}

	public function getCreditNoteDate(): ?\DateTimeInterface
	{
		return $this->creditNoteDate;
	}

	public function setCreditNoteDate(?\DateTimeInterface $creditNoteDate): self
	{
		$this->creditNoteDate = $creditNoteDate;

		return $this;
	}

	public function getCreditNoteNumberModified(): ?bool
	{
		return $this->creditNoteNumberModified;
	}

	public function setCreditNoteNumberModified(?bool $creditNoteNumberModified): self
	{
		$this->creditNoteNumberModified = $creditNoteNumberModified;

		return $this;
	}

	public function getOriginalTasksValue(): ?string
	{
		return $this->originalTasksValue;
	}

	public function setOriginalTasksValue(?string $originalTasksValue): self
	{
		$this->originalTasksValue = $originalTasksValue;

		return $this;
	}
}
