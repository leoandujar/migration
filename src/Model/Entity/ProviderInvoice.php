<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'provider_invoice')]
#[ORM\UniqueConstraint(name: 'provider_invoice_internal_number_key', columns: ['internal_number'])]
#[ORM\Entity]
class ProviderInvoice implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'provider_invoice_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'provider_invoice_id', type: 'bigint')]
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

	#[ORM\Column(name: 'activities_id_numbers', type: 'text', nullable: true)]
	private ?string $activitiesIdNumbers;

	#[ORM\Column(name: 'activities_value', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $activitiesValue;

	#[ORM\Column(name: 'internal_number', type: 'string', nullable: true)]
	private ?string $internalNumber;

	#[ORM\Column(name: 'state', type: 'string', nullable: false)]
	private string $state;

	#[ORM\Column(name: 'notes_from_provider', type: 'text', nullable: true)]
	private ?string $notesFromProvider;

	#[ORM\Column(name: 'provider_invoice_file_path', type: 'text', nullable: true)]
	private ?string $providerInvoiceFilePath;

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

	#[ORM\ManyToOne(targetEntity: ProviderPerson::class)]
	#[ORM\JoinColumn(name: 'accountency_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?ProviderPerson $accountencyPerson;

	#[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'providerInvoices')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: true)]
	private ?Provider $provider;

	#[ORM\Column(name: 'specification_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $specificationDate;

	#[ORM\Column(name: 'invoice_upload_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $invoiceUploadDate;

	#[ORM\Column(name: 'vat_calculation_rule', type: 'string', nullable: false, options: ['default' => 'SUM_ITEMS::character varying'])]
	private string $vatCalculationRule;

	#[ORM\Column(name: 'append_currency_to_total_in_words', type: 'boolean', nullable: true)]
	private ?bool $appendCurrencyToTotalInWords;

	#[ORM\Column(name: 'autocalculate_payment_conditions', type: 'boolean', nullable: true)]
	private ?bool $autocalculatePaymentConditions;

	#[ORM\Column(name: 'autocalculate_payment_conditions_description', type: 'boolean', nullable: true)]
	private ?bool $autocalculatePaymentConditionsDescription;

	#[ORM\Column(name: 'use_converter', type: 'boolean', nullable: true)]
	private ?bool $useConverter;

	#[ORM\Column(name: 'visible_in_vp', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $visibleInVp;

	#[ORM\Column(name: 'backup_old_exchange_ratio_not_used', type: 'decimal', precision: 19, scale: 10, nullable: true)]
	private ?float $backupOldExchangeRatioNotUsed;

	#[ORM\Column(name: 'exchange_ratio_event', type: 'string', nullable: true)]
	private ?string $exchangeRatioEvent;

	#[ORM\Column(name: 'locale', type: 'string', nullable: false)]
	private string $locale;

	#[ORM\ManyToOne(targetEntity: Vendor::class, inversedBy: 'outcomeDocuments')]
	#[ORM\JoinColumn(name: 'vendor_id', referencedColumnName: 'id', nullable: true)]
	private ?Vendor $vendor;

	#[ORM\OneToMany(targetEntity: ProviderCharge::class, mappedBy: 'providerInvoice', orphanRemoval: true)]
	private mixed $providerCharge;

	#[ORM\JoinTable(name: 'provider_invoice_categories')]
	#[ORM\JoinColumn(name: 'provider_invoice_id', referencedColumnName: 'provider_invoice_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'provider_invoice_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'providersInvoice')]
	protected mixed $categories;

	public function __construct()
	{
		$this->providerCharge = new ArrayCollection();
		$this->categories     = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getActivitiesIdNumbers(): ?string
	{
		return $this->activitiesIdNumbers;
	}

	/**
	 * @return mixed
	 */
	public function setActivitiesIdNumbers(?string $activitiesIdNumbers): self
	{
		$this->activitiesIdNumbers = $activitiesIdNumbers;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getActivitiesValue(): ?string
	{
		return $this->activitiesValue;
	}

	/**
	 * @return mixed
	 */
	public function setActivitiesValue(?string $activitiesValue): self
	{
		$this->activitiesValue = $activitiesValue;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getInternalNumber(): ?string
	{
		return $this->internalNumber;
	}

	/**
	 * @return mixed
	 */
	public function setInternalNumber(?string $internalNumber): self
	{
		$this->internalNumber = $internalNumber;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getProviderInvoiceFilePath(): ?string
	{
		return $this->providerInvoiceFilePath;
	}

	/**
	 * @return mixed
	 */
	public function setProviderInvoiceFilePath(?string $providerInvoiceFilePath): self
	{
		$this->providerInvoiceFilePath = $providerInvoiceFilePath;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getSpecificationDate(): ?\DateTimeInterface
	{
		return $this->specificationDate;
	}

	/**
	 * @return mixed
	 */
	public function setSpecificationDate(?\DateTimeInterface $specificationDate): self
	{
		$this->specificationDate = $specificationDate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getInvoiceUploadDate(): ?\DateTimeInterface
	{
		return $this->invoiceUploadDate;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceUploadDate(?\DateTimeInterface $invoiceUploadDate): self
	{
		$this->invoiceUploadDate = $invoiceUploadDate;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getAccountencyPerson(): ?ProviderPerson
	{
		return $this->accountencyPerson;
	}

	/**
	 * @return mixed
	 */
	public function setAccountencyPerson(?ProviderPerson $accountencyPerson): self
	{
		$this->accountencyPerson = $accountencyPerson;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getVendor(): ?Vendor
	{
		return $this->vendor;
	}

	/**
	 * @return mixed
	 */
	public function setVendor(?Vendor $vendor): self
	{
		$this->vendor = $vendor;

		return $this;
	}

	/**
	 * @return Collection|ProviderCharge[]
	 */
	public function getProviderCharge(): Collection
	{
		return $this->providerCharge;
	}

	/**
	 * @return mixed
	 */
	public function addProviderCharge(ProviderCharge $providerCharge): self
	{
		if (!$this->providerCharge->contains($providerCharge)) {
			$this->providerCharge[] = $providerCharge;
			$providerCharge->setProviderInvoice($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProviderCharge(ProviderCharge $providerCharge): self
	{
		if ($this->providerCharge->removeElement($providerCharge)) {
			// set the owning side to null (unless already changed)
			if ($providerCharge->getProviderInvoice() === $this) {
				$providerCharge->setProviderInvoice(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Category[]
	 */
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
		$this->categories->removeElement($category);

		return $this;
	}
}
