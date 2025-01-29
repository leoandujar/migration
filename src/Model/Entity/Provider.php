<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'provider')]
#[ORM\UniqueConstraint(name: 'provider_id_number_key', columns: ['id_number'])]
#[ORM\UniqueConstraint(name: 'provider_enrolment_directory_key', columns: ['enrolment_directory'])]
#[ORM\Entity]
class Provider implements EntityInterface
{
	public const STATUS_ACTIVE = 'ACTIVE';
	public const STATUS_INACTIVE = 'INACTIVE';
	public const STATUS_WAITING_FOR_APPROVAL = 'WAITING_FOR_APPROVAL';
	public const STATUS_REJECTED = 'REJECTED';
	public const STATUS_TOO_EXPENSIVE = 'TOO_EXPENSIVE';
	public const STATUS_INCOMPLETE_DATA = 'INCOMPLETE_DATA';
	public const STATUS_POTENTIAL = 'POTENTIAL';
	public const STATUS_NOT_CONFIRMED = 'NOT_CONFIRMED';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'provider_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'provider_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'erased_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $erasedAt;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'acceptance_of_terms_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $acceptanceOfTermsDate;

	#[ORM\Column(name: 'acceptance_of_terms_type', type: 'string', nullable: true)]
	private ?string $acceptanceOfTermsType;

	#[ORM\Column(name: 'address_email', type: 'string', nullable: false)]
	private string $addressEmail;

	#[ORM\Column(name: 'address_email2', type: 'string', nullable: true)]
	private ?string $addressEmail2;

	#[ORM\Column(name: 'address_email3', type: 'string', nullable: true)]
	private ?string $addressEmail3;

	#[ORM\Column(name: 'address_fax', type: 'string', nullable: true)]
	private ?string $addressFax;

	#[ORM\Column(name: 'address_mobile_phone', type: 'string', nullable: true)]
	private ?string $addressMobilePhone;

	#[ORM\Column(name: 'address_phone', type: 'string', nullable: true)]
	private ?string $addressPhone;

	#[ORM\Column(name: 'address_phone2', type: 'string', nullable: true)]
	private ?string $addressPhone2;

	#[ORM\Column(name: 'address_phone3', type: 'string', nullable: true)]
	private ?string $addressPhone3;

	#[ORM\Column(name: 'send_cc_to_email_2', type: 'boolean', nullable: true)]
	private ?bool $sendCcToEmail2;

	#[ORM\Column(name: 'send_cc_to_email_3', type: 'boolean', nullable: true)]
	private ?bool $sendCcToEmail3;

	#[ORM\Column(name: 'address_sms_enabled', type: 'boolean', nullable: true)]
	private ?bool $addressSmsEnabled;

	#[ORM\Column(name: 'time_zone', type: 'string', nullable: true)]
	private ?string $timeZone;

	#[ORM\Column(name: 'address_www', type: 'string', nullable: true)]
	private ?string $addressWww;

	#[ORM\Column(name: 'address_www2', type: 'string', nullable: true)]
	private ?string $addressWww2;

	#[ORM\Column(name: 'address_address', type: 'string', nullable: true)]
	private ?string $addressAddress;

	#[ORM\Column(name: 'address_address_2', type: 'string', nullable: true)]
	private ?string $addressAddress2;

	#[ORM\Column(name: 'address_city', type: 'string', nullable: true)]
	private ?string $addressCity;

	#[ORM\Column(name: 'address_dependent_locality', type: 'string', nullable: true)]
	private ?string $addressDependentLocality;

	#[ORM\Column(name: 'address_sorting_code', type: 'string', nullable: true)]
	private ?string $addressSortingCode;

	#[ORM\Column(name: 'address_zipcode', type: 'string', nullable: true)]
	private ?string $addressZipcode;

	#[ORM\Column(name: 'cc_in_emails_to_contact_persons', type: 'boolean', nullable: true)]
	private ?bool $ccInEmailsToContactPersons;

	#[ORM\Column(name: 'contract_number', type: 'string', nullable: true)]
	private ?string $contractNumber;

	#[ORM\Column(name: 'correspondence_address', type: 'string', nullable: true)]
	private ?string $correspondenceAddress;

	#[ORM\Column(name: 'correspondence_address_2', type: 'string', nullable: true)]
	private ?string $correspondenceAddress2;

	#[ORM\Column(name: 'correspondence_city', type: 'string', nullable: true)]
	private ?string $correspondenceCity;

	#[ORM\Column(name: 'correspondence_dependent_locality', type: 'string', nullable: true)]
	private ?string $correspondenceDependentLocality;

	#[ORM\Column(name: 'correspondence_sorting_code', type: 'string', nullable: true)]
	private ?string $correspondenceSortingCode;

	#[ORM\Column(name: 'correspondence_zipcode', type: 'string', nullable: true)]
	private ?string $correspondenceZipcode;

	#[ORM\Column(name: 'actual_draft_date_reference', type: 'string', nullable: false)]
	private string $actualDraftDateReference;

	#[ORM\Column(name: 'actual_draft_n_days', type: 'smallint', nullable: false)]
	private int $actualDraftNDays;

	#[ORM\Column(name: 'actual_draft_end_of_month', type: 'boolean', nullable: false)]
	private bool $actualDraftEndOfMonth;

	#[ORM\Column(name: 'actual_draft_m_months', type: 'smallint', nullable: false)]
	private int $actualDraftMMonths;

	#[ORM\Column(name: 'actual_final_date_reference', type: 'string', nullable: false)]
	private string $actualFinalDateReference;

	#[ORM\Column(name: 'actual_final_n_days', type: 'smallint', nullable: false)]
	private int $actualFinalNDays;

	#[ORM\Column(name: 'actual_final_end_of_month', type: 'boolean', nullable: false)]
	private bool $actualFinalEndOfMonth;

	#[ORM\Column(name: 'actual_final_m_months', type: 'smallint', nullable: false)]
	private int $actualFinalMMonths;

	#[ORM\Column(name: 'expected_draft_date_reference', type: 'string', nullable: false)]
	private string $expectedDraftDateReference;

	#[ORM\Column(name: 'expected_draft_n_days', type: 'smallint', nullable: false)]
	private int $expectedDraftNDays;

	#[ORM\Column(name: 'expected_draft_end_of_month', type: 'boolean', nullable: false)]
	private bool $expectedDraftEndOfMonth;

	#[ORM\Column(name: 'expected_draft_m_months', type: 'smallint', nullable: false)]
	private int $expectedDraftMMonths;

	#[ORM\Column(name: 'expected_final_date_reference', type: 'string', nullable: false)]
	private string $expectedFinalDateReference;

	#[ORM\Column(name: 'expected_final_n_days', type: 'smallint', nullable: false)]
	private int $expectedFinalNDays;

	#[ORM\Column(name: 'expected_final_end_of_month', type: 'boolean', nullable: false)]
	private bool $expectedFinalEndOfMonth;

	#[ORM\Column(name: 'expected_final_m_months', type: 'smallint', nullable: false)]
	private int $expectedFinalMMonths;

	#[ORM\Column(name: 'use_draft', type: 'boolean', nullable: true)]
	private ?bool $useDraft;

	#[ORM\Column(name: 'first_contact_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $firstContactDate;

	#[ORM\Column(name: 'full_name', type: 'string', nullable: false)]
	private string $fullName;

	#[ORM\Column(name: 'full_name_normalized', type: 'string', nullable: false)]
	private string $fullNameNormalized;

	#[ORM\Column(name: 'id_number', type: 'string', nullable: false)]
	private string $idNumber;

	#[ORM\Column(name: 'invoice_note', type: 'text', nullable: true)]
	private ?string $invoiceNote;

	#[ORM\Column(name: 'no_crm_emails', type: 'boolean', nullable: true)]
	private ?bool $noCrmEmails;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'sales_notes', type: 'text', nullable: true)]
	private ?string $salesNotes;

	#[ORM\Column(name: 'single_person', type: 'boolean', nullable: true)]
	private ?string $singlePerson;

	#[ORM\Column(name: 'tax_no_2', type: 'string', nullable: true)]
	private ?string $taxNo2;

	#[ORM\Column(name: 'use_address_as_correspondence', type: 'boolean', nullable: true)]
	private ?bool $useAddressAsCorrespondence;

	#[ORM\Column(name: 'use_default_dates_calculation_rules', type: 'boolean', nullable: true)]
	private ?bool $useDefaultDatesCalculationRules;

	#[ORM\Column(name: 'account_on_provider_server', type: 'text', nullable: true)]
	private ?string $accountOnProviderServer;

	#[ORM\Column(name: 'first_last_dates_updated_on', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstLastDatesUpdatedOn;

	#[ORM\Column(name: 'first_project_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstProjectDate;

	#[ORM\Column(name: 'first_project_date_auto', type: 'boolean', nullable: true)]
	private ?bool $firstProjectDateAuto;

	#[ORM\Column(name: 'in_house', type: 'boolean', nullable: false)]
	private bool $inHouse;

	#[ORM\Column(name: 'invoice_activities', type: 'boolean', nullable: true)]
	private ?bool $invoiceActivities;

	#[ORM\Column(name: 'last_project_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastProjectDate;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'name_normalized', type: 'string', nullable: false)]
	private string $nameNormalized;

	#[ORM\Column(name: 'number_of_activities', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $numberOfActivities;

	#[ORM\Column(name: 'enrolment_directory', type: 'string', length: 1800, nullable: true)]
	private ?string $enrolmentDirectory;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\Column(name: 'provider_type', type: 'string', nullable: false)]
	private string $providerType;

	#[ORM\OneToOne(targetEntity: SystemAccount::class)]
	#[ORM\JoinColumn(name: 'system_account_id', referencedColumnName: 'system_account_id', nullable: true)]
	private ?SystemAccount $systemAccount;

	#[ORM\ManyToOne(targetEntity: Country::class)]
	#[ORM\JoinColumn(name: 'address_country_id', referencedColumnName: 'country_id', nullable: true)]
	private ?Country $addressCountry;

	#[ORM\ManyToOne(targetEntity: Province::class)]
	#[ORM\JoinColumn(name: 'address_province_id', referencedColumnName: 'province_id', nullable: true)]
	private ?Province $addressProvince;

	#[ORM\ManyToOne(targetEntity: Branch::class)]
	#[ORM\JoinColumn(name: 'branch_id', referencedColumnName: 'branch_id', nullable: false)]
	private Branch $branch;

	#[ORM\ManyToOne(targetEntity: Country::class)]
	#[ORM\JoinColumn(name: 'correspondence_country_id', referencedColumnName: 'country_id', nullable: true)]
	private ?Country $correspondenceCountry;

	#[ORM\ManyToOne(targetEntity: Province::class)]
	#[ORM\JoinColumn(name: 'correspondence_province_id', referencedColumnName: 'province_id', nullable: true)]
	private ?Province $correspondenceProvince;

	#[ORM\ManyToOne(targetEntity: PaymentCondition::class)]
	#[ORM\JoinColumn(name: 'default_payment_conditions_id', referencedColumnName: 'payment_conditions_id', nullable: false)]
	private PaymentCondition $defaultPaymentConditions;

	#[ORM\ManyToOne(targetEntity: PaymentCondition::class)]
	#[ORM\JoinColumn(name: 'default_payment_conditions_id_for_empty_invoice', referencedColumnName: 'payment_conditions_id', nullable: false)]
	private PaymentCondition $defaultPaymentConditionsEmptyInvoice;

	#[ORM\ManyToOne(targetEntity: LeadSource::class)]
	#[ORM\JoinColumn(name: 'lead_source_id', referencedColumnName: 'lead_source_id', nullable: true)]
	private ?LeadSource $leadSource;

	#[ORM\Column(name: 'preferred_social_media_contact_id', type: 'bigint', nullable: true)]
	private ?string $preferredSocialMediaContactId;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\Column(name: 'social_media_collection_id', type: 'bigint', nullable: true)]
	private ?string $socialMediaCollectionId;

	#[ORM\ManyToOne(targetEntity: VatRate::class)]
	#[ORM\JoinColumn(name: 'vat_rate_id', referencedColumnName: 'vat_rate_id', nullable: true)]
	private ?VatRate $vatRate;

	#[ORM\ManyToOne(targetEntity: ProviderPerson::class)]
	#[ORM\JoinColumn(name: 'accountency_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?ProviderPerson $accountencyContactPerson;

	#[ORM\Column(name: 'automated_activity_action_id', type: 'bigint', nullable: true)]
	private ?string $automatedActivityActionId;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\Column(name: 'evaluation_template_id', type: 'bigint', nullable: true)]
	private ?string $evaluationTemplateId;

	#[ORM\Column(name: 'invoice_template_id', type: 'bigint', nullable: true)]
	private ?string $invoiceTemplateId;

	#[ORM\Column(name: 'multiple_purchase_order_template_id', type: 'bigint', nullable: true)]
	private ?string $multiplePurchaseOrderTemplateId;

	#[ORM\Column(name: 'preferences_id', type: 'bigint', nullable: true)]
	private ?string $preferencesId;

	#[ORM\Column(name: 'provider_rating_id', type: 'bigint', nullable: true)]
	private ?string $providerRatingId;

	#[ORM\Column(name: 'purchase_order_template_id', type: 'bigint', nullable: true)]
	private ?string $purchaseOrderTemplateId;

	#[ORM\Column(name: 'provider_experience_id', type: 'bigint', nullable: true)]
	private ?string $providerExperienceId;

	#[ORM\Column(name: 'number_of_completed_activities', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $numberOfCompletedActivities;

	#[ORM\Column(name: 'number_of_quote_activities', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $numberOfQuoteActivities;

	#[ORM\Column(name: 'previous_activity_ready_email_template_id', type: 'bigint', nullable: true)]
	private ?string $previousActivityReadyEmailTemplateId;

	#[ORM\Column(name: 'previous_activity_partially_finished_email_template_id', type: 'bigint', nullable: true)]
	private ?string $previousActivityPartiallyFinishedEmailTemplateId;

	#[ORM\Column(name: 'link_account_identifier', type: 'text', nullable: true)]
	private ?string $linkAccountIdentifier;

	#[ORM\Column(name: 'has_avatar', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $hasAvatar;

	#[ORM\Column(name: 'xtm_uid', type: 'text', nullable: true)]
	private ?string $xtmUid;

	#[ORM\OneToMany(targetEntity: ProviderLanguageCombination::class, mappedBy: 'provider')]
	private mixed $languageCombinations;

	#[ORM\OneToMany(targetEntity: ProviderInvoice::class, mappedBy: 'provider')]
	private mixed $providerInvoices;

	#[ORM\OneToMany(targetEntity: ProviderPayment::class, mappedBy: 'provider')]
	private mixed $providerPayments;

	#[ORM\OneToMany(targetEntity: ProviderPerson::class, mappedBy: 'provider')]
	private mixed $providerPersons;

	#[ORM\OneToMany(targetEntity: Account::class, mappedBy: 'provider')]
	private mixed $accounts;

	#[ORM\ManyToMany(targetEntity: Feedback::class, mappedBy: 'providers', cascade: ['persist'])]
	private mixed $feedbacks;

	#[ORM\JoinTable(name: 'provider_categories')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'project_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'providers')]
	protected mixed $categories;

	#[ORM\Column(name: 'qbo_provider_id', type: 'string', nullable: true)]
	private ?string $qboProvider;

	public function __construct()
	{
		$this->languageCombinations = new ArrayCollection();
		$this->providerInvoices = new ArrayCollection();
		$this->providerPayments = new ArrayCollection();
		$this->providerPersons = new ArrayCollection();
		$this->accounts = new ArrayCollection();
		$this->feedbacks = new ArrayCollection();
		$this->categories = new ArrayCollection();
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

	public function getAcceptanceOfTermsDate(): ?\DateTimeInterface
	{
		return $this->acceptanceOfTermsDate;
	}

	/**
	 * @return mixed
	 */
	public function setAcceptanceOfTermsDate(?\DateTimeInterface $acceptanceOfTermsDate): self
	{
		$this->acceptanceOfTermsDate = $acceptanceOfTermsDate;

		return $this;
	}

	public function getAcceptanceOfTermsType(): ?string
	{
		return $this->acceptanceOfTermsType;
	}

	/**
	 * @return mixed
	 */
	public function setAcceptanceOfTermsType(?string $acceptanceOfTermsType): self
	{
		$this->acceptanceOfTermsType = $acceptanceOfTermsType;

		return $this;
	}

	public function getAddressEmail(): ?string
	{
		return $this->addressEmail;
	}

	/**
	 * @return mixed
	 */
	public function setAddressEmail(string $addressEmail): self
	{
		$this->addressEmail = $addressEmail;

		return $this;
	}

	public function getAddressEmail2(): ?string
	{
		return $this->addressEmail2;
	}

	/**
	 * @return mixed
	 */
	public function setAddressEmail2(?string $addressEmail2): self
	{
		$this->addressEmail2 = $addressEmail2;

		return $this;
	}

	public function getAddressEmail3(): ?string
	{
		return $this->addressEmail3;
	}

	/**
	 * @return mixed
	 */
	public function setAddressEmail3(?string $addressEmail3): self
	{
		$this->addressEmail3 = $addressEmail3;

		return $this;
	}

	public function getAddressFax(): ?string
	{
		return $this->addressFax;
	}

	/**
	 * @return mixed
	 */
	public function setAddressFax(?string $addressFax): self
	{
		$this->addressFax = $addressFax;

		return $this;
	}

	public function getAddressMobilePhone(): ?string
	{
		return $this->addressMobilePhone;
	}

	/**
	 * @return mixed
	 */
	public function setAddressMobilePhone(?string $addressMobilePhone): self
	{
		$this->addressMobilePhone = $addressMobilePhone;

		return $this;
	}

	public function getAddressPhone(): ?string
	{
		return $this->addressPhone;
	}

	/**
	 * @return mixed
	 */
	public function setAddressPhone(?string $addressPhone): self
	{
		$this->addressPhone = $addressPhone;

		return $this;
	}

	public function getAddressPhone2(): ?string
	{
		return $this->addressPhone2;
	}

	/**
	 * @return mixed
	 */
	public function setAddressPhone2(?string $addressPhone2): self
	{
		$this->addressPhone2 = $addressPhone2;

		return $this;
	}

	public function getAddressPhone3(): ?string
	{
		return $this->addressPhone3;
	}

	/**
	 * @return mixed
	 */
	public function setAddressPhone3(?string $addressPhone3): self
	{
		$this->addressPhone3 = $addressPhone3;

		return $this;
	}

	public function getSendCcToEmail2(): ?bool
	{
		return $this->sendCcToEmail2;
	}

	/**
	 * @return mixed
	 */
	public function setSendCcToEmail2(?bool $sendCcToEmail2): self
	{
		$this->sendCcToEmail2 = $sendCcToEmail2;

		return $this;
	}

	public function getSendCcToEmail3(): ?bool
	{
		return $this->sendCcToEmail3;
	}

	/**
	 * @return mixed
	 */
	public function setSendCcToEmail3(?bool $sendCcToEmail3): self
	{
		$this->sendCcToEmail3 = $sendCcToEmail3;

		return $this;
	}

	public function getAddressSmsEnabled(): ?bool
	{
		return $this->addressSmsEnabled;
	}

	/**
	 * @return mixed
	 */
	public function setAddressSmsEnabled(?bool $addressSmsEnabled): self
	{
		$this->addressSmsEnabled = $addressSmsEnabled;

		return $this;
	}

	public function getTimeZone(): ?string
	{
		return $this->timeZone;
	}

	/**
	 * @return mixed
	 */
	public function setTimeZone(?string $timeZone): self
	{
		$this->timeZone = $timeZone;

		return $this;
	}

	public function getAddressWww(): ?string
	{
		return $this->addressWww;
	}

	/**
	 * @return mixed
	 */
	public function setAddressWww(?string $addressWww): self
	{
		$this->addressWww = $addressWww;

		return $this;
	}

	public function getAddressWww2(): ?string
	{
		return $this->addressWww2;
	}

	/**
	 * @return mixed
	 */
	public function setAddressWww2(?string $addressWww2): self
	{
		$this->addressWww2 = $addressWww2;

		return $this;
	}

	public function getAddressAddress(): ?string
	{
		return $this->addressAddress;
	}

	/**
	 * @return mixed
	 */
	public function setAddressAddress(?string $addressAddress): self
	{
		$this->addressAddress = $addressAddress;

		return $this;
	}

	public function getAddressAddress2(): ?string
	{
		return $this->addressAddress2;
	}

	/**
	 * @return mixed
	 */
	public function setAddressAddress2(?string $addressAddress2): self
	{
		$this->addressAddress2 = $addressAddress2;

		return $this;
	}

	public function getAddressCity(): ?string
	{
		return $this->addressCity;
	}

	/**
	 * @return mixed
	 */
	public function setAddressCity(?string $addressCity): self
	{
		$this->addressCity = $addressCity;

		return $this;
	}

	public function getAddressDependentLocality(): ?string
	{
		return $this->addressDependentLocality;
	}

	/**
	 * @return mixed
	 */
	public function setAddressDependentLocality(?string $addressDependentLocality): self
	{
		$this->addressDependentLocality = $addressDependentLocality;

		return $this;
	}

	public function getAddressSortingCode(): ?string
	{
		return $this->addressSortingCode;
	}

	/**
	 * @return mixed
	 */
	public function setAddressSortingCode(?string $addressSortingCode): self
	{
		$this->addressSortingCode = $addressSortingCode;

		return $this;
	}

	public function getAddressZipcode(): ?string
	{
		return $this->addressZipcode;
	}

	/**
	 * @return mixed
	 */
	public function setAddressZipcode(?string $addressZipcode): self
	{
		$this->addressZipcode = $addressZipcode;

		return $this;
	}

	public function getCcInEmailsToContactPersons(): ?bool
	{
		return $this->ccInEmailsToContactPersons;
	}

	/**
	 * @return mixed
	 */
	public function setCcInEmailsToContactPersons(?bool $ccInEmailsToContactPersons): self
	{
		$this->ccInEmailsToContactPersons = $ccInEmailsToContactPersons;

		return $this;
	}

	public function getContractNumber(): ?string
	{
		return $this->contractNumber;
	}

	/**
	 * @return mixed
	 */
	public function setContractNumber(?string $contractNumber): self
	{
		$this->contractNumber = $contractNumber;

		return $this;
	}

	public function getCorrespondenceAddress(): ?string
	{
		return $this->correspondenceAddress;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceAddress(?string $correspondenceAddress): self
	{
		$this->correspondenceAddress = $correspondenceAddress;

		return $this;
	}

	public function getCorrespondenceAddress2(): ?string
	{
		return $this->correspondenceAddress2;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceAddress2(?string $correspondenceAddress2): self
	{
		$this->correspondenceAddress2 = $correspondenceAddress2;

		return $this;
	}

	public function getCorrespondenceCity(): ?string
	{
		return $this->correspondenceCity;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceCity(?string $correspondenceCity): self
	{
		$this->correspondenceCity = $correspondenceCity;

		return $this;
	}

	public function getCorrespondenceDependentLocality(): ?string
	{
		return $this->correspondenceDependentLocality;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceDependentLocality(?string $correspondenceDependentLocality): self
	{
		$this->correspondenceDependentLocality = $correspondenceDependentLocality;

		return $this;
	}

	public function getCorrespondenceSortingCode(): ?string
	{
		return $this->correspondenceSortingCode;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceSortingCode(?string $correspondenceSortingCode): self
	{
		$this->correspondenceSortingCode = $correspondenceSortingCode;

		return $this;
	}

	public function getCorrespondenceZipcode(): ?string
	{
		return $this->correspondenceZipcode;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceZipcode(?string $correspondenceZipcode): self
	{
		$this->correspondenceZipcode = $correspondenceZipcode;

		return $this;
	}

	public function getActualDraftDateReference(): ?string
	{
		return $this->actualDraftDateReference;
	}

	/**
	 * @return mixed
	 */
	public function setActualDraftDateReference(string $actualDraftDateReference): self
	{
		$this->actualDraftDateReference = $actualDraftDateReference;

		return $this;
	}

	public function getActualDraftNDays(): ?int
	{
		return $this->actualDraftNDays;
	}

	/**
	 * @return mixed
	 */
	public function setActualDraftNDays(int $actualDraftNDays): self
	{
		$this->actualDraftNDays = $actualDraftNDays;

		return $this;
	}

	public function getActualDraftEndOfMonth(): ?bool
	{
		return $this->actualDraftEndOfMonth;
	}

	/**
	 * @return mixed
	 */
	public function setActualDraftEndOfMonth(bool $actualDraftEndOfMonth): self
	{
		$this->actualDraftEndOfMonth = $actualDraftEndOfMonth;

		return $this;
	}

	public function getActualDraftMMonths(): ?int
	{
		return $this->actualDraftMMonths;
	}

	/**
	 * @return mixed
	 */
	public function setActualDraftMMonths(int $actualDraftMMonths): self
	{
		$this->actualDraftMMonths = $actualDraftMMonths;

		return $this;
	}

	public function getActualFinalDateReference(): ?string
	{
		return $this->actualFinalDateReference;
	}

	/**
	 * @return mixed
	 */
	public function setActualFinalDateReference(string $actualFinalDateReference): self
	{
		$this->actualFinalDateReference = $actualFinalDateReference;

		return $this;
	}

	public function getActualFinalNDays(): ?int
	{
		return $this->actualFinalNDays;
	}

	/**
	 * @return mixed
	 */
	public function setActualFinalNDays(int $actualFinalNDays): self
	{
		$this->actualFinalNDays = $actualFinalNDays;

		return $this;
	}

	public function getActualFinalEndOfMonth(): ?bool
	{
		return $this->actualFinalEndOfMonth;
	}

	/**
	 * @return mixed
	 */
	public function setActualFinalEndOfMonth(bool $actualFinalEndOfMonth): self
	{
		$this->actualFinalEndOfMonth = $actualFinalEndOfMonth;

		return $this;
	}

	public function getActualFinalMMonths(): ?int
	{
		return $this->actualFinalMMonths;
	}

	/**
	 * @return mixed
	 */
	public function setActualFinalMMonths(int $actualFinalMMonths): self
	{
		$this->actualFinalMMonths = $actualFinalMMonths;

		return $this;
	}

	public function getExpectedDraftDateReference(): ?string
	{
		return $this->expectedDraftDateReference;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedDraftDateReference(string $expectedDraftDateReference): self
	{
		$this->expectedDraftDateReference = $expectedDraftDateReference;

		return $this;
	}

	public function getExpectedDraftNDays(): ?int
	{
		return $this->expectedDraftNDays;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedDraftNDays(int $expectedDraftNDays): self
	{
		$this->expectedDraftNDays = $expectedDraftNDays;

		return $this;
	}

	public function getExpectedDraftEndOfMonth(): ?bool
	{
		return $this->expectedDraftEndOfMonth;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedDraftEndOfMonth(bool $expectedDraftEndOfMonth): self
	{
		$this->expectedDraftEndOfMonth = $expectedDraftEndOfMonth;

		return $this;
	}

	public function getExpectedDraftMMonths(): ?int
	{
		return $this->expectedDraftMMonths;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedDraftMMonths(int $expectedDraftMMonths): self
	{
		$this->expectedDraftMMonths = $expectedDraftMMonths;

		return $this;
	}

	public function getExpectedFinalDateReference(): ?string
	{
		return $this->expectedFinalDateReference;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedFinalDateReference(string $expectedFinalDateReference): self
	{
		$this->expectedFinalDateReference = $expectedFinalDateReference;

		return $this;
	}

	public function getExpectedFinalNDays(): ?int
	{
		return $this->expectedFinalNDays;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedFinalNDays(int $expectedFinalNDays): self
	{
		$this->expectedFinalNDays = $expectedFinalNDays;

		return $this;
	}

	public function getExpectedFinalEndOfMonth(): ?bool
	{
		return $this->expectedFinalEndOfMonth;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedFinalEndOfMonth(bool $expectedFinalEndOfMonth): self
	{
		$this->expectedFinalEndOfMonth = $expectedFinalEndOfMonth;

		return $this;
	}

	public function getExpectedFinalMMonths(): ?int
	{
		return $this->expectedFinalMMonths;
	}

	/**
	 * @return mixed
	 */
	public function setExpectedFinalMMonths(int $expectedFinalMMonths): self
	{
		$this->expectedFinalMMonths = $expectedFinalMMonths;

		return $this;
	}

	public function getUseDraft(): ?bool
	{
		return $this->useDraft;
	}

	/**
	 * @return mixed
	 */
	public function setUseDraft(?bool $useDraft): self
	{
		$this->useDraft = $useDraft;

		return $this;
	}

	public function getFirstContactDate(): ?\DateTimeInterface
	{
		return $this->firstContactDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstContactDate(?\DateTimeInterface $firstContactDate): self
	{
		$this->firstContactDate = $firstContactDate;

		return $this;
	}

	public function getFullName(): ?string
	{
		return $this->fullName;
	}

	/**
	 * @return mixed
	 */
	public function setFullName(string $fullName): self
	{
		$this->fullName = $fullName;

		return $this;
	}

	public function getFullNameNormalized(): ?string
	{
		return $this->fullNameNormalized;
	}

	/**
	 * @return mixed
	 */
	public function setFullNameNormalized(string $fullNameNormalized): self
	{
		$this->fullNameNormalized = $fullNameNormalized;

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

	public function getNoCrmEmails(): ?bool
	{
		return $this->noCrmEmails;
	}

	/**
	 * @return mixed
	 */
	public function setNoCrmEmails(?bool $noCrmEmails): self
	{
		$this->noCrmEmails = $noCrmEmails;

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

	public function getSalesNotes(): ?string
	{
		return $this->salesNotes;
	}

	/**
	 * @return mixed
	 */
	public function setSalesNotes(?string $salesNotes): self
	{
		$this->salesNotes = $salesNotes;

		return $this;
	}

	public function getSinglePerson(): ?bool
	{
		return $this->singlePerson;
	}

	/**
	 * @return mixed
	 */
	public function setSinglePerson(?bool $singlePerson): self
	{
		$this->singlePerson = $singlePerson;

		return $this;
	}

	public function getTaxNo2(): ?string
	{
		return $this->taxNo2;
	}

	/**
	 * @return mixed
	 */
	public function setTaxNo2(?string $taxNo2): self
	{
		$this->taxNo2 = $taxNo2;

		return $this;
	}

	public function getUseAddressAsCorrespondence(): ?bool
	{
		return $this->useAddressAsCorrespondence;
	}

	/**
	 * @return mixed
	 */
	public function setUseAddressAsCorrespondence(?bool $useAddressAsCorrespondence): self
	{
		$this->useAddressAsCorrespondence = $useAddressAsCorrespondence;

		return $this;
	}

	public function getUseDefaultDatesCalculationRules(): ?bool
	{
		return $this->useDefaultDatesCalculationRules;
	}

	/**
	 * @return mixed
	 */
	public function setUseDefaultDatesCalculationRules(?bool $useDefaultDatesCalculationRules): self
	{
		$this->useDefaultDatesCalculationRules = $useDefaultDatesCalculationRules;

		return $this;
	}

	public function getAccountOnProviderServer(): ?string
	{
		return $this->accountOnProviderServer;
	}

	/**
	 * @return mixed
	 */
	public function setAccountOnProviderServer(?string $accountOnProviderServer): self
	{
		$this->accountOnProviderServer = $accountOnProviderServer;

		return $this;
	}

	public function getFirstLastDatesUpdatedOn(): ?\DateTimeInterface
	{
		return $this->firstLastDatesUpdatedOn;
	}

	/**
	 * @return mixed
	 */
	public function setFirstLastDatesUpdatedOn(?\DateTimeInterface $firstLastDatesUpdatedOn): self
	{
		$this->firstLastDatesUpdatedOn = $firstLastDatesUpdatedOn;

		return $this;
	}

	public function getFirstProjectDate(): ?\DateTimeInterface
	{
		return $this->firstProjectDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstProjectDate(?\DateTimeInterface $firstProjectDate): self
	{
		$this->firstProjectDate = $firstProjectDate;

		return $this;
	}

	public function getFirstProjectDateAuto(): ?bool
	{
		return $this->firstProjectDateAuto;
	}

	/**
	 * @return mixed
	 */
	public function setFirstProjectDateAuto(?bool $firstProjectDateAuto): self
	{
		$this->firstProjectDateAuto = $firstProjectDateAuto;

		return $this;
	}

	public function getInHouse(): ?bool
	{
		return $this->inHouse;
	}

	/**
	 * @return mixed
	 */
	public function setInHouse(bool $inHouse): self
	{
		$this->inHouse = $inHouse;

		return $this;
	}

	public function getInvoiceActivities(): ?bool
	{
		return $this->invoiceActivities;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceActivities(?bool $invoiceActivities): self
	{
		$this->invoiceActivities = $invoiceActivities;

		return $this;
	}

	public function getLastProjectDate(): ?\DateTimeInterface
	{
		return $this->lastProjectDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastProjectDate(?\DateTimeInterface $lastProjectDate): self
	{
		$this->lastProjectDate = $lastProjectDate;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getNameNormalized(): ?string
	{
		return $this->nameNormalized;
	}

	/**
	 * @return mixed
	 */
	public function setNameNormalized(string $nameNormalized): self
	{
		$this->nameNormalized = $nameNormalized;

		return $this;
	}

	public function getNumberOfActivities(): ?int
	{
		return $this->numberOfActivities;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfActivities(int $numberOfActivities): self
	{
		$this->numberOfActivities = $numberOfActivities;

		return $this;
	}

	public function getEnrolmentDirectory(): ?string
	{
		return $this->enrolmentDirectory;
	}

	/**
	 * @return mixed
	 */
	public function setEnrolmentDirectory(?string $enrolmentDirectory): self
	{
		$this->enrolmentDirectory = $enrolmentDirectory;

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

	public function getProviderType(): ?string
	{
		return $this->providerType;
	}

	/**
	 * @return mixed
	 */
	public function setProviderType(string $providerType): self
	{
		$this->providerType = $providerType;

		return $this;
	}

	public function getPreferredSocialMediaContactId(): ?string
	{
		return $this->preferredSocialMediaContactId;
	}

	/**
	 * @return mixed
	 */
	public function setPreferredSocialMediaContactId(?string $preferredSocialMediaContactId): self
	{
		$this->preferredSocialMediaContactId = $preferredSocialMediaContactId;

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

	public function getSocialMediaCollectionId(): ?string
	{
		return $this->socialMediaCollectionId;
	}

	/**
	 * @return mixed
	 */
	public function setSocialMediaCollectionId(?string $socialMediaCollectionId): self
	{
		$this->socialMediaCollectionId = $socialMediaCollectionId;

		return $this;
	}

	public function getAutomatedActivityActionId(): ?string
	{
		return $this->automatedActivityActionId;
	}

	/**
	 * @return mixed
	 */
	public function setAutomatedActivityActionId(?string $automatedActivityActionId): self
	{
		$this->automatedActivityActionId = $automatedActivityActionId;

		return $this;
	}

	public function getEvaluationTemplateId(): ?string
	{
		return $this->evaluationTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setEvaluationTemplateId(?string $evaluationTemplateId): self
	{
		$this->evaluationTemplateId = $evaluationTemplateId;

		return $this;
	}

	public function getInvoiceTemplateId(): ?string
	{
		return $this->invoiceTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setInvoiceTemplateId(?string $invoiceTemplateId): self
	{
		$this->invoiceTemplateId = $invoiceTemplateId;

		return $this;
	}

	public function getMultiplePurchaseOrderTemplateId(): ?string
	{
		return $this->multiplePurchaseOrderTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setMultiplePurchaseOrderTemplateId(?string $multiplePurchaseOrderTemplateId): self
	{
		$this->multiplePurchaseOrderTemplateId = $multiplePurchaseOrderTemplateId;

		return $this;
	}

	public function getPreferencesId(): ?string
	{
		return $this->preferencesId;
	}

	/**
	 * @return mixed
	 */
	public function setPreferencesId(?string $preferencesId): self
	{
		$this->preferencesId = $preferencesId;

		return $this;
	}

	public function getProviderRatingId(): ?string
	{
		return $this->providerRatingId;
	}

	/**
	 * @return mixed
	 */
	public function setProviderRatingId(?string $providerRatingId): self
	{
		$this->providerRatingId = $providerRatingId;

		return $this;
	}

	public function getPurchaseOrderTemplateId(): ?string
	{
		return $this->purchaseOrderTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setPurchaseOrderTemplateId(?string $purchaseOrderTemplateId): self
	{
		$this->purchaseOrderTemplateId = $purchaseOrderTemplateId;

		return $this;
	}

	public function getProviderExperienceId(): ?string
	{
		return $this->providerExperienceId;
	}

	/**
	 * @return mixed
	 */
	public function setProviderExperienceId(?string $providerExperienceId): self
	{
		$this->providerExperienceId = $providerExperienceId;

		return $this;
	}

	public function getNumberOfCompletedActivities(): ?int
	{
		return $this->numberOfCompletedActivities;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfCompletedActivities(int $numberOfCompletedActivities): self
	{
		$this->numberOfCompletedActivities = $numberOfCompletedActivities;

		return $this;
	}

	public function getNumberOfQuoteActivities(): ?int
	{
		return $this->numberOfQuoteActivities;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfQuoteActivities(int $numberOfQuoteActivities): self
	{
		$this->numberOfQuoteActivities = $numberOfQuoteActivities;

		return $this;
	}

	public function getPreviousActivityReadyEmailTemplateId(): ?string
	{
		return $this->previousActivityReadyEmailTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setPreviousActivityReadyEmailTemplateId(?string $previousActivityReadyEmailTemplateId): self
	{
		$this->previousActivityReadyEmailTemplateId = $previousActivityReadyEmailTemplateId;

		return $this;
	}

	public function getPreviousActivityPartiallyFinishedEmailTemplateId(): ?string
	{
		return $this->previousActivityPartiallyFinishedEmailTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setPreviousActivityPartiallyFinishedEmailTemplateId(?string $previousActivityPartiallyFinishedEmailTemplateId): self
	{
		$this->previousActivityPartiallyFinishedEmailTemplateId = $previousActivityPartiallyFinishedEmailTemplateId;

		return $this;
	}

	public function getLinkAccountIdentifier(): ?string
	{
		return $this->linkAccountIdentifier;
	}

	/**
	 * @return mixed
	 */
	public function setLinkAccountIdentifier(?string $linkAccountIdentifier): self
	{
		$this->linkAccountIdentifier = $linkAccountIdentifier;

		return $this;
	}

	public function getHasAvatar(): ?bool
	{
		return $this->hasAvatar;
	}

	/**
	 * @return mixed
	 */
	public function setHasAvatar(bool $hasAvatar): self
	{
		$this->hasAvatar = $hasAvatar;

		return $this;
	}

	public function getXtmUid(): ?string
	{
		return $this->xtmUid;
	}

	/**
	 * @return mixed
	 */
	public function setXtmUid(?string $xtmUid): self
	{
		$this->xtmUid = $xtmUid;

		return $this;
	}

	public function getSystemAccount(): ?SystemAccount
	{
		return $this->systemAccount;
	}

	/**
	 * @return mixed
	 */
	public function setSystemAccount(?SystemAccount $systemAccount): self
	{
		$this->systemAccount = $systemAccount;

		return $this;
	}

	public function getAddressCountry(): ?Country
	{
		return $this->addressCountry;
	}

	/**
	 * @return mixed
	 */
	public function setAddressCountry(?Country $addressCountry): self
	{
		$this->addressCountry = $addressCountry;

		return $this;
	}

	public function getAddressProvince(): ?Province
	{
		return $this->addressProvince;
	}

	/**
	 * @return mixed
	 */
	public function setAddressProvince(?Province $addressProvince): self
	{
		$this->addressProvince = $addressProvince;

		return $this;
	}

	public function getBranch(): ?Branch
	{
		return $this->branch;
	}

	/**
	 * @return mixed
	 */
	public function setBranch(?Branch $branch): self
	{
		$this->branch = $branch;

		return $this;
	}

	public function getCorrespondenceCountry(): ?Country
	{
		return $this->correspondenceCountry;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceCountry(?Country $correspondenceCountry): self
	{
		$this->correspondenceCountry = $correspondenceCountry;

		return $this;
	}

	public function getCorrespondenceProvince(): ?Province
	{
		return $this->correspondenceProvince;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceProvince(?Province $correspondenceProvince): self
	{
		$this->correspondenceProvince = $correspondenceProvince;

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

	public function getDefaultPaymentConditionsEmptyInvoice(): ?PaymentCondition
	{
		return $this->defaultPaymentConditionsEmptyInvoice;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultPaymentConditionsEmptyInvoice(?PaymentCondition $defaultPaymentConditionsEmptyInvoice): self
	{
		$this->defaultPaymentConditionsEmptyInvoice = $defaultPaymentConditionsEmptyInvoice;

		return $this;
	}

	public function getLeadSource(): ?LeadSource
	{
		return $this->leadSource;
	}

	/**
	 * @return mixed
	 */
	public function setLeadSource(?LeadSource $leadSource): self
	{
		$this->leadSource = $leadSource;

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

	public function getAccountencyContactPerson(): ?ProviderPerson
	{
		return $this->accountencyContactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setAccountencyContactPerson(?ProviderPerson $accountencyContactPerson): self
	{
		$this->accountencyContactPerson = $accountencyContactPerson;

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

	public function getLanguageCombinations(): Collection
	{
		return $this->languageCombinations;
	}

	/**
	 * @return mixed
	 */
	public function addLanguageCombination(ProviderLanguageCombination $languageCombination): self
	{
		if (!$this->languageCombinations->contains($languageCombination)) {
			$this->languageCombinations[] = $languageCombination;
			$languageCombination->setProvider($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLanguageCombination(ProviderLanguageCombination $languageCombination): self
	{
		if ($this->languageCombinations->contains($languageCombination)) {
			$this->languageCombinations->removeElement($languageCombination);
			// set the owning side to null (unless already changed)
			if ($languageCombination->getProvider() === $this) {
				$languageCombination->setProvider(null);
			}
		}

		return $this;
	}

	public function getProviderInvoices(): Collection
	{
		return $this->providerInvoices;
	}

	/**
	 * @return mixed
	 */
	public function addProviderInvoice(ProviderInvoice $providerInvoice): self
	{
		if (!$this->providerInvoices->contains($providerInvoice)) {
			$this->providerInvoices[] = $providerInvoice;
			$providerInvoice->setProvider($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProviderInvoice(ProviderInvoice $providerInvoice): self
	{
		if ($this->providerInvoices->contains($providerInvoice)) {
			$this->providerInvoices->removeElement($providerInvoice);
			// set the owning side to null (unless already changed)
			if ($providerInvoice->getProvider() === $this) {
				$providerInvoice->setProvider(null);
			}
		}

		return $this;
	}

	public function getProviderPayments(): Collection
	{
		return $this->providerPayments;
	}

	/**
	 * @return mixed
	 */
	public function addProviderPayment(ProviderPayment $providerPayment): self
	{
		if (!$this->providerPayments->contains($providerPayment)) {
			$this->providerPayments[] = $providerPayment;
			$providerPayment->setProvider($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProviderPayment(ProviderPayment $providerPayment): self
	{
		if ($this->providerPayments->contains($providerPayment)) {
			$this->providerPayments->removeElement($providerPayment);
			// set the owning side to null (unless already changed)
			if ($providerPayment->getProvider() === $this) {
				$providerPayment->setProvider(null);
			}
		}

		return $this;
	}

	public function getProviderPersons(): Collection
	{
		return $this->providerPersons;
	}

	/**
	 * @return mixed
	 */
	public function addProviderPerson(ProviderPerson $providerPerson): self
	{
		if (!$this->providerPersons->contains($providerPerson)) {
			$this->providerPersons[] = $providerPerson;
			$providerPerson->setProvider($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProviderPerson(ProviderPerson $providerPerson): self
	{
		if ($this->providerPersons->contains($providerPerson)) {
			$this->providerPersons->removeElement($providerPerson);
			// set the owning side to null (unless already changed)
			if ($providerPerson->getProvider() === $this) {
				$providerPerson->setProvider(null);
			}
		}

		return $this;
	}

	public function getAccounts(): Collection
	{
		return $this->accounts;
	}

	/**
	 * @return mixed
	 */
	public function addAccount(Account $account): self
	{
		if (!$this->accounts->contains($account)) {
			$this->accounts[] = $account;
			$account->setProvider($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeAccount(Account $account): self
	{
		if ($this->accounts->contains($account)) {
			$this->accounts->removeElement($account);
			// set the owning side to null (unless already changed)
			if ($account->getProvider() === $this) {
				$account->setProvider(null);
			}
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
			$feedback->addProvider($this);
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
			$feedback->removeProvider($this);
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

	public function getQboProvider(): ?string
	{
		return $this->qboProvider;
	}

	/**
	 * @return mixed
	 */
	public function setQboProvider(?string $qboProvider): self
	{
		$this->qboProvider = $qboProvider;

		return $this;
	}

	public function getErasedAt(): ?\DateTimeInterface
	{
		return $this->erasedAt;
	}

	/**
	 * @return mixed
	 */
	public function setErasedAt(?\DateTimeInterface $erasedAt): self
	{
		$this->erasedAt = $erasedAt;

		return $this;
	}
}
