<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'customer')]
#[ORM\UniqueConstraint(name: 'customer_name_key', columns: ['name'])]
#[ORM\UniqueConstraint(name: 'customer_name_normalized_key', columns: ['name_normalized'])]
#[ORM\UniqueConstraint(name: 'customer_id_number_key', columns: ['id_number'])]
#[ORM\UniqueConstraint(name: 'customer_enrolment_directory_key', columns: ['enrolment_directory'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\CustomerRepository')]
class Customer implements EntityInterface
{
	public const STATUS_ACTIVE = 'ACTIVE';
	public const STATUS_INACTIVE = 'INACTIVE';
	public const STATUS_POTENTIAL = 'POTENTIAL';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'erased_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $erasedAt;

	#[ORM\Column(name: 'last_login_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastLoginDate;

	#[ORM\Column(name: 'last_failed_login_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastFailedLoginDate;

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
	private ?string $timezone;

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
	private ?string $addressZipCode;

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
	private ?string $correspondenceZipCode;

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
	private ?bool $singlePerson;

	#[ORM\Column(name: 'use_default_user_group', type: 'boolean', nullable: false)]
	private bool $useDefaultUserGroup;

	#[ORM\Column(name: 'use_address_as_correspondence', type: 'boolean', nullable: true)]
	private ?bool $useAddressAsCorrespondence;

	#[ORM\Column(name: 'enrolment_directory', type: 'string', length: 1800, nullable: true)]
	private ?string $enrolmentDirectory;

	#[ORM\Column(name: 'first_last_dates_updated_on', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstLastDatesUpdatedOn;

	#[ORM\Column(name: 'first_project_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstProjectDate;

	#[ORM\Column(name: 'first_quote_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstQuoteDate;

	#[ORM\Column(name: 'last_project_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastProjectDate;

	#[ORM\Column(name: 'last_quote_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastQuoteDate;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'name_normalized', type: 'string', nullable: false)]
	private string $nameNormalized;

	#[ORM\Column(name: 'non_payer', type: 'boolean', nullable: true)]
	private ?bool $nonPayer;

	#[ORM\Column(name: 'number_of_projects', type: 'integer', nullable: false, options: ['default' => '0'])]
	private int $numberOfProjects;

	#[ORM\Column(name: 'number_of_quotes', type: 'integer', nullable: false, options: ['default' => '0'])]
	private int $numberOfQuotes;

	#[ORM\Column(name: 'potential_annual_revenue_generation', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $potentialAnnualRevenueGeneration;

	#[ORM\Column(name: 'potential_annual_revenue_generation_update_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $potentialAnnualRevenueGenerationUpdateDate;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\Column(name: 'tax_no_1', type: 'string', nullable: true)]
	private ?string $taxNo1;

	#[ORM\Column(name: 'tax_no_2', type: 'string', nullable: true)]
	private ?string $taxNo2;

	#[ORM\Column(name: 'tax_no_3', type: 'string', nullable: true)]
	private ?string $taxNo3;

	#[ORM\Column(name: 'valid_tax_no_1', type: 'boolean', nullable: true)]
	private ?bool $validTaxNo1;

	#[ORM\Column(name: 'wire_transfer', type: 'smallint', nullable: true)]
	private ?int $wireTransfer;

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

	#[ORM\ManyToOne(targetEntity: VatRate::class)]
	#[ORM\JoinColumn(name: 'vat_rate_id', referencedColumnName: 'vat_rate_id', nullable: true)]
	private ?VatRate $vatRate;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'accountency_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $accountencyContactPerson;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'in_house_am_responsible_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $inHouseAmResponsible;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'in_house_pc_responsible_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $inHousePcResponsible;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'in_house_pm_responsible_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $inHousePmResponsible;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'in_house_sp_responsible_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $inHouseSpResponsible;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'parent_customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $parentCustomer;

	#[ORM\Column(name: 'preferences_id', type: 'bigint', nullable: true)]
	private string $preferences;

	#[ORM\ManyToOne(targetEntity: XtrfUserGroup::class)]
	#[ORM\JoinColumn(name: 'xtrf_user_group_id', referencedColumnName: 'xtrf_user_group_id', nullable: true)]
	private ?XtrfUserGroup $userGroup;

	#[ORM\ManyToOne(targetEntity: CustomerPriceProfile::class)]
	#[ORM\JoinColumn(name: 'customer_portal_price_profile', referencedColumnName: 'customer_price_profile_id', nullable: true)]
	private ?CustomerPriceProfile $customerPortalPriceProfile;

	#[ORM\Column(name: 'link_account_identifier', type: 'text', nullable: true)]
	private ?string $linkAccountIdentifier;

	#[ORM\Column(name: 'limit_access_to_people_responsible', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $limitAccessToPeopleResponsible;

	#[ORM\Column(name: 'use_default_customer_services', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $useDefaultCustomerServices;

	#[ORM\Column(name: 'use_default_customer_services_workflows', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $useDefaultCustomerServicesWorkflows;

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

	#[ORM\Column(name: 'use_default_dates_calculation_rules', type: 'boolean', nullable: true)]
	private ?bool $useDefaultDatesCalculationRules;

	#[ORM\Column(name: 'create_default_term_if_needed', type: 'boolean', nullable: true)]
	private ?bool $createDefaultTermIfneeded;

	#[ORM\Column(name: 'create_default_tm_if_needed', type: 'boolean', nullable: true)]
	private ?bool $createDefaultTmIfNeeded;

	#[ORM\Column(name: 'first_project_date_auto', type: 'boolean', nullable: true)]
	private ?bool $firstProjectDateAuto;

	#[ORM\Column(name: 'first_quote_date_auto', type: 'boolean', nullable: true)]
	private ?bool $firstQuoteDateAuto;

	#[ORM\Column(name: 'use_default_customer_language_specializations', type: 'boolean', nullable: true)]
	private ?bool $useDefaultCustomerLanguageSpecializations;

	#[ORM\Column(name: 'use_default_customer_languages', type: 'boolean', nullable: true)]
	private ?bool $useDefaultCustomerLanguages;

	#[ORM\Column(name: 'send_invoice_email', type: 'boolean', nullable: true)]
	private ?bool $sendInvoiceEmail;

	#[ORM\Column(name: 'budget_code_required_when_adding_quote_or_project', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $budgetCodeRequiredWhenAddingQuoteOrProject;

	#[ORM\Column(name: 'has_avatar', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $hasAvatar;

	#[ORM\Column(name: 'used_checking_type', type: 'string', nullable: true)]
	private ?string $usedCheckingType;

	#[ORM\Column(name: 'preferred_social_media_contact_id', type: 'bigint', nullable: true)]
	private ?string $preferredSocialMediaContactId;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\Column(name: 'social_media_collection_id', type: 'bigint', nullable: true)]
	private ?string $socialMediaCollectionId;

	#[ORM\Column(name: 'draft_invoice_numbering_schema_id', type: 'bigint', nullable: true)]
	private ?string $draftInvoiceNumberingSchemaId;

	#[ORM\Column(name: 'draft_invoice_template_id', type: 'bigint', nullable: true)]
	private ?string $draftInvoiceTemplateId;

	#[ORM\Column(name: 'final_invoice_numbering_schema_id', type: 'bigint', nullable: true)]
	private ?string $finalInvoiceNumberingSchemaId;

	#[ORM\Column(name: 'final_invoice_template_id', type: 'bigint', nullable: true)]
	private ?string $finalInvoiceTemplateId;

	#[ORM\Column(name: 'project_confirmation_template_id', type: 'bigint', nullable: true)]
	private ?string $projectConfirmationTemplateId;

	#[ORM\Column(name: 'quote_confirmation_template_id', type: 'bigint', nullable: true)]
	private ?string $quoteConfirmationTemplateId;

	#[ORM\Column(name: 'quote_task_confirmation_template_id', type: 'bigint', nullable: true)]
	private ?string $quoteTaskConfirmationTemplateId;

	#[ORM\Column(name: 'task_confirmation_template_id', type: 'bigint', nullable: true)]
	private ?string $taskConfirmationTemplateId;

	#[ORM\Column(name: 'task_files_available_email_template_id', type: 'bigint', nullable: true)]
	private ?string $taskFilesAvailableEmailTemplateId;

	#[ORM\Column(name: 'customer_salesforce_id', type: 'bigint', nullable: true)]
	private ?string $customerSalesforceId;

	#[ORM\Column(name: 'linked_provider_id', type: 'bigint', nullable: true)]
	private ?string $linkedProviderId;

	#[ORM\Column(name: 'account_on_customer_server', type: 'text', nullable: true)]
	private ?string $accountOnCustomerServer;

	#[ORM\Column(name: 'turnaround', type: 'text', nullable: true)]
	private ?string $turnaround;

	#[ORM\Column(name: 'contract_volume', type: 'string', nullable: true)]
	private ?string $contractVolume;

	#[ORM\Column(name: 'cost_center_departments', type: 'string', nullable: true)]
	private ?string $costCenterDepartments;

	#[ORM\Column(name: 'sales_target_persona_a', type: 'text', nullable: true)]
	private ?string $salesTargetPersonaA;

	#[ORM\Column(name: 'sales_target_persona_b', type: 'text', nullable: true)]
	private ?string $salesTargetPersonaB;

	#[ORM\Column(name: 'sales_target_persona_c', type: 'text', nullable: true)]
	private ?string $salesTargetPersonaC;

	#[ORM\Column(name: 'contract_amount', precision: 40, scale: 10, nullable: true)]
	private ?float $contractAmount;

	#[ORM\Column(name: 'end_date_contract', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $endDateContract;

	#[ORM\Column(name: 'gln', type: 'string', nullable: true)]
	private ?string $gln;

	#[ORM\Column(name: 'kp_region', type: 'string', nullable: true)]
	private ?string $kpRegion;

	#[ORM\Column(name: 'vizient_id', type: 'string', nullable: true)]
	private ?string $vizientId;

	#[ORM\Column(name: 'project_code', type: 'string', nullable: true)]
	private ?string $projectCode;

	#[ORM\Column(name: 'reporting_group', type: 'string', nullable: true)]
	private ?string $reportingGroup;

	#[ORM\Column(name: 'sales_cut_off_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $salesCutOffDate;

	#[ORM\Column(name: 'xtm_cid', type: 'string', nullable: true)]
	private ?string $xtmCid;

	#[ORM\Column(name: 'account_type', type: 'string', nullable: true)]
	private ?string $accountType;

	#[ORM\Column(name: 'am_assigned', type: 'string', nullable: true)]
	private ?string $amAssigned;

	#[ORM\Column(name: 'qbo_id', type: 'integer', nullable: true)]
	private ?int $qboId;

	#[ORM\Column(name: 'new_business', type: 'string', nullable: true)]
	private ?string $newBusiness;

	#[ORM\Column(name: 'commission_start_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $comissionStartDate;

	#[ORM\Column(name: 'commission_status_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $comissionStatusDate;

	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $chartGroups;

	#[ORM\Column(name: 'category_groups', type: 'json', nullable: true)]
	private ?array $categoryGroups;

	#[ORM\JoinTable(name: 'customer_categories')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'project_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'customers', cascade: ['persist'])]
	protected mixed $categories;

	#[ORM\JoinTable(name: 'customer_industries')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'industry_id', referencedColumnName: 'industry_id')]
	#[ORM\ManyToMany(targetEntity: Industry::class, inversedBy: 'customers', cascade: ['persist'])]
	protected mixed $industries;

	#[ORM\ManyToMany(targetEntity: CustomerPerson::class, mappedBy: 'customers', cascade: ['persist'])]
	private mixed $customerPersons;

	#[ORM\ManyToMany(targetEntity: CustomerAccountencyContactPerson::class, mappedBy: 'customer', cascade: ['persist'])]
	private mixed $customerAccountencyPersons;

	#[ORM\JoinTable(name: 'customer_additional_persons_responsible')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'xtrf_user_id', referencedColumnName: 'xtrf_user_id')]
	#[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'customersAdditionalPersons', cascade: ['persist'])]
	protected mixed $usersPersonsResponsible;

	#[ORM\JoinTable(name: 'customer_language_specializations')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'language_specialization_id', referencedColumnName: 'language_specialization_id')]
	#[ORM\ManyToMany(targetEntity: LanguageSpecialization::class, inversedBy: 'customers', cascade: ['persist'])]
	protected mixed $languageSpecializations;

	#[ORM\JoinTable(name: 'customer_languages')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'xtrf_language_id', referencedColumnName: 'xtrf_language_id')]
	#[ORM\ManyToMany(targetEntity: XtrfLanguage::class, inversedBy: 'customers', cascade: ['persist'])]
    #[ORM\OrderBy(["name" => "ASC"])]
    protected mixed $languages;

	#[ORM\OneToMany(targetEntity: CustomerService::class, mappedBy: 'customer', cascade: ['persist'])]
	protected mixed $services;

	#[ORM\OneToMany(targetEntity: CustomerPerson::class, mappedBy: 'customer')]
	private mixed $contactPersons;

	#[ORM\OneToMany(targetEntity: CustomerInvoice::class, mappedBy: 'customer')]
	private mixed $invoices;

	#[ORM\OneToOne(targetEntity: CPSetting::class, mappedBy: 'customer', cascade: ['persist'])]
	private ?CPSetting $settings;

	#[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'customer')]
	private mixed $projects;

	#[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'cpCustomer', cascade: ['persist', 'remove'])]
	private mixed $permissions;

	#[ORM\Column(name: 'roles', type: 'json', length: 100, nullable: true)]
	private ?array $roles = [];

	#[ORM\OneToOne(targetEntity: BlCustomer::class, mappedBy: 'customer')]
	private BlCustomer $blCustomer;

	#[ORM\OneToOne(targetEntity: AVCustomerRule::class, mappedBy: 'customer')]
	private ?AVCustomerRule $rules;

	#[ORM\Column(name: 'credit_note_numbering_schema_id', type: 'bigint', nullable: true)]
	private ?string $creditNoteNumberingSchemaId;

	#[ORM\Column(name: 'credit_note_template_id', type: 'bigint', nullable: true)]
	private ?string $creditNoteTemplateId;

	#[ORM\Column(name: 'client_portal_interface_locale', type: 'string', nullable: true)]
	private ?string $clientPortalInterfaceLocale;

	public function __construct()
	{
		$this->categories = new ArrayCollection();
		$this->industries = new ArrayCollection();
		$this->customerPersons = new ArrayCollection();
		$this->customerAccountencyPersons = new ArrayCollection();
		$this->usersPersonsResponsible = new ArrayCollection();
		$this->languageSpecializations = new ArrayCollection();
		$this->languages = new ArrayCollection();
		$this->contactPersons = new ArrayCollection();
		$this->invoices = new ArrayCollection();
		$this->projects = new ArrayCollection();
		$this->permissions = new ArrayCollection();
		$this->services = new ArrayCollection();
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

	public function getTimezone(): ?string
	{
		return $this->timezone;
	}

	/**
	 * @return mixed
	 */
	public function setTimezone(?string $timezone): self
	{
		$this->timezone = $timezone;

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

	public function getAddressZipCode(): ?string
	{
		return $this->addressZipCode;
	}

	/**
	 * @return mixed
	 */
	public function setAddressZipCode(?string $addressZipCode): self
	{
		$this->addressZipCode = $addressZipCode;

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

	public function getCorrespondenceZipCode(): ?string
	{
		return $this->correspondenceZipCode;
	}

	/**
	 * @return mixed
	 */
	public function setCorrespondenceZipCode(?string $correspondenceZipCode): self
	{
		$this->correspondenceZipCode = $correspondenceZipCode;

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

	public function getUseDefaultUserGroup(): ?bool
	{
		return $this->useDefaultUserGroup;
	}

	/**
	 * @return mixed
	 */
	public function setUseDefaultUserGroup(bool $useDefaultUserGroup): self
	{
		$this->useDefaultUserGroup = $useDefaultUserGroup;

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

	public function getFirstQuoteDate(): ?\DateTimeInterface
	{
		return $this->firstQuoteDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstQuoteDate(?\DateTimeInterface $firstQuoteDate): self
	{
		$this->firstQuoteDate = $firstQuoteDate;

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

	public function getLastQuoteDate(): ?\DateTimeInterface
	{
		return $this->lastQuoteDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastQuoteDate(?\DateTimeInterface $lastQuoteDate): self
	{
		$this->lastQuoteDate = $lastQuoteDate;

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

	public function getNonPayer(): ?bool
	{
		return $this->nonPayer;
	}

	/**
	 * @return mixed
	 */
	public function setNonPayer(?bool $nonPayer): self
	{
		$this->nonPayer = $nonPayer;

		return $this;
	}

	public function getNumberOfProjects(): ?int
	{
		return $this->numberOfProjects;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfProjects(int $numberOfProjects): self
	{
		$this->numberOfProjects = $numberOfProjects;

		return $this;
	}

	public function getNumberOfQuotes(): ?int
	{
		return $this->numberOfQuotes;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfQuotes(int $numberOfQuotes): self
	{
		$this->numberOfQuotes = $numberOfQuotes;

		return $this;
	}

	public function getPotentialAnnualRevenueGeneration(): ?string
	{
		return $this->potentialAnnualRevenueGeneration;
	}

	/**
	 * @return mixed
	 */
	public function setPotentialAnnualRevenueGeneration(?string $potentialAnnualRevenueGeneration): self
	{
		$this->potentialAnnualRevenueGeneration = $potentialAnnualRevenueGeneration;

		return $this;
	}

	public function getPotentialAnnualRevenueGenerationUpdateDate(): ?\DateTimeInterface
	{
		return $this->potentialAnnualRevenueGenerationUpdateDate;
	}

	/**
	 * @return mixed
	 */
	public function setPotentialAnnualRevenueGenerationUpdateDate(?\DateTimeInterface $potentialAnnualRevenueGenerationUpdateDate): self
	{
		$this->potentialAnnualRevenueGenerationUpdateDate = $potentialAnnualRevenueGenerationUpdateDate;

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

	public function getTaxNo1(): ?string
	{
		return $this->taxNo1;
	}

	/**
	 * @return mixed
	 */
	public function setTaxNo1(?string $taxNo1): self
	{
		$this->taxNo1 = $taxNo1;

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

	public function getTaxNo3(): ?string
	{
		return $this->taxNo3;
	}

	/**
	 * @return mixed
	 */
	public function setTaxNo3(?string $taxNo3): self
	{
		$this->taxNo3 = $taxNo3;

		return $this;
	}

	public function getValidTaxNo1(): ?bool
	{
		return $this->validTaxNo1;
	}

	/**
	 * @return mixed
	 */
	public function setValidTaxNo1(?bool $validTaxNo1): self
	{
		$this->validTaxNo1 = $validTaxNo1;

		return $this;
	}

	public function getWireTransfer(): ?int
	{
		return $this->wireTransfer;
	}

	/**
	 * @return mixed
	 */
	public function setWireTransfer(?int $wireTransfer): self
	{
		$this->wireTransfer = $wireTransfer;

		return $this;
	}

	public function getPreferences(): ?string
	{
		return $this->preferences;
	}

	/**
	 * @return mixed
	 */
	public function setPreferences(?string $preferences): self
	{
		$this->preferences = $preferences;

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

	public function getLimitAccessToPeopleResponsible(): ?bool
	{
		return $this->limitAccessToPeopleResponsible;
	}

	/**
	 * @return mixed
	 */
	public function setLimitAccessToPeopleResponsible(bool $limitAccessToPeopleResponsible): self
	{
		$this->limitAccessToPeopleResponsible = $limitAccessToPeopleResponsible;

		return $this;
	}

	public function getUseDefaultCustomerServices(): ?bool
	{
		return $this->useDefaultCustomerServices;
	}

	/**
	 * @return mixed
	 */
	public function setUseDefaultCustomerServices(bool $useDefaultCustomerServices): self
	{
		$this->useDefaultCustomerServices = $useDefaultCustomerServices;

		return $this;
	}

	public function getUseDefaultCustomerServicesWorkflows(): ?bool
	{
		return $this->useDefaultCustomerServicesWorkflows;
	}

	/**
	 * @return mixed
	 */
	public function setUseDefaultCustomerServicesWorkflows(bool $useDefaultCustomerServicesWorkflows): self
	{
		$this->useDefaultCustomerServicesWorkflows = $useDefaultCustomerServicesWorkflows;

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

	public function getCreateDefaultTermIfneeded(): ?bool
	{
		return $this->createDefaultTermIfneeded;
	}

	/**
	 * @return mixed
	 */
	public function setCreateDefaultTermIfneeded(?bool $createDefaultTermIfneeded): self
	{
		$this->createDefaultTermIfneeded = $createDefaultTermIfneeded;

		return $this;
	}

	public function getCreateDefaultTmIfNeeded(): ?bool
	{
		return $this->createDefaultTmIfNeeded;
	}

	/**
	 * @return mixed
	 */
	public function setCreateDefaultTmIfNeeded(?bool $createDefaultTmIfNeeded): self
	{
		$this->createDefaultTmIfNeeded = $createDefaultTmIfNeeded;

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

	public function getFirstQuoteDateAuto(): ?bool
	{
		return $this->firstQuoteDateAuto;
	}

	/**
	 * @return mixed
	 */
	public function setFirstQuoteDateAuto(?bool $firstQuoteDateAuto): self
	{
		$this->firstQuoteDateAuto = $firstQuoteDateAuto;

		return $this;
	}

	public function getUseDefaultCustomerLanguageSpecializations(): ?bool
	{
		return $this->useDefaultCustomerLanguageSpecializations;
	}

	/**
	 * @return mixed
	 */
	public function setUseDefaultCustomerLanguageSpecializations(?bool $useDefaultCustomerLanguageSpecializations): self
	{
		$this->useDefaultCustomerLanguageSpecializations = $useDefaultCustomerLanguageSpecializations;

		return $this;
	}

	public function getUseDefaultCustomerLanguages(): ?bool
	{
		return $this->useDefaultCustomerLanguages;
	}

	/**
	 * @return mixed
	 */
	public function setUseDefaultCustomerLanguages(?bool $useDefaultCustomerLanguages): self
	{
		$this->useDefaultCustomerLanguages = $useDefaultCustomerLanguages;

		return $this;
	}

	public function getSendInvoiceEmail(): ?bool
	{
		return $this->sendInvoiceEmail;
	}

	/**
	 * @return mixed
	 */
	public function setSendInvoiceEmail(?bool $sendInvoiceEmail): self
	{
		$this->sendInvoiceEmail = $sendInvoiceEmail;

		return $this;
	}

	public function getBudgetCodeRequiredWhenAddingQuoteOrProject(): ?bool
	{
		return $this->budgetCodeRequiredWhenAddingQuoteOrProject;
	}

	/**
	 * @return mixed
	 */
	public function setBudgetCodeRequiredWhenAddingQuoteOrProject(bool $budgetCodeRequiredWhenAddingQuoteOrProject): self
	{
		$this->budgetCodeRequiredWhenAddingQuoteOrProject = $budgetCodeRequiredWhenAddingQuoteOrProject;

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

	public function getUsedCheckingType(): ?string
	{
		return $this->usedCheckingType;
	}

	/**
	 * @return mixed
	 */
	public function setUsedCheckingType(?string $usedCheckingType): self
	{
		$this->usedCheckingType = $usedCheckingType;

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

	public function getDraftInvoiceNumberingSchemaId(): ?string
	{
		return $this->draftInvoiceNumberingSchemaId;
	}

	/**
	 * @return mixed
	 */
	public function setDraftInvoiceNumberingSchemaId(?string $draftInvoiceNumberingSchemaId): self
	{
		$this->draftInvoiceNumberingSchemaId = $draftInvoiceNumberingSchemaId;

		return $this;
	}

	public function getDraftInvoiceTemplateId(): ?string
	{
		return $this->draftInvoiceTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setDraftInvoiceTemplateId(?string $draftInvoiceTemplateId): self
	{
		$this->draftInvoiceTemplateId = $draftInvoiceTemplateId;

		return $this;
	}

	public function getFinalInvoiceNumberingSchemaId(): ?string
	{
		return $this->finalInvoiceNumberingSchemaId;
	}

	/**
	 * @return mixed
	 */
	public function setFinalInvoiceNumberingSchemaId(?string $finalInvoiceNumberingSchemaId): self
	{
		$this->finalInvoiceNumberingSchemaId = $finalInvoiceNumberingSchemaId;

		return $this;
	}

	public function getFinalInvoiceTemplateId(): ?string
	{
		return $this->finalInvoiceTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setFinalInvoiceTemplateId(?string $finalInvoiceTemplateId): self
	{
		$this->finalInvoiceTemplateId = $finalInvoiceTemplateId;

		return $this;
	}

	public function getProjectConfirmationTemplateId(): ?string
	{
		return $this->projectConfirmationTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setProjectConfirmationTemplateId(?string $projectConfirmationTemplateId): self
	{
		$this->projectConfirmationTemplateId = $projectConfirmationTemplateId;

		return $this;
	}

	public function getQuoteConfirmationTemplateId(): ?string
	{
		return $this->quoteConfirmationTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setQuoteConfirmationTemplateId(?string $quoteConfirmationTemplateId): self
	{
		$this->quoteConfirmationTemplateId = $quoteConfirmationTemplateId;

		return $this;
	}

	public function getQuoteTaskConfirmationTemplateId(): ?string
	{
		return $this->quoteTaskConfirmationTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setQuoteTaskConfirmationTemplateId(?string $quoteTaskConfirmationTemplateId): self
	{
		$this->quoteTaskConfirmationTemplateId = $quoteTaskConfirmationTemplateId;

		return $this;
	}

	public function getTaskConfirmationTemplateId(): ?string
	{
		return $this->taskConfirmationTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setTaskConfirmationTemplateId(?string $taskConfirmationTemplateId): self
	{
		$this->taskConfirmationTemplateId = $taskConfirmationTemplateId;

		return $this;
	}

	public function getTaskFilesAvailableEmailTemplateId(): ?string
	{
		return $this->taskFilesAvailableEmailTemplateId;
	}

	/**
	 * @return mixed
	 */
	public function setTaskFilesAvailableEmailTemplateId(?string $taskFilesAvailableEmailTemplateId): self
	{
		$this->taskFilesAvailableEmailTemplateId = $taskFilesAvailableEmailTemplateId;

		return $this;
	}

	public function getCustomerSalesforceId(): ?string
	{
		return $this->customerSalesforceId;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerSalesforceId(?string $customerSalesforceId): self
	{
		$this->customerSalesforceId = $customerSalesforceId;

		return $this;
	}

	public function getLinkedProviderId(): ?string
	{
		return $this->linkedProviderId;
	}

	/**
	 * @return mixed
	 */
	public function setLinkedProviderId(?string $linkedProviderId): self
	{
		$this->linkedProviderId = $linkedProviderId;

		return $this;
	}

	public function getAccountOnCustomerServer(): ?string
	{
		return $this->accountOnCustomerServer;
	}

	/**
	 * @return mixed
	 */
	public function setAccountOnCustomerServer(?string $accountOnCustomerServer): self
	{
		$this->accountOnCustomerServer = $accountOnCustomerServer;

		return $this;
	}

	public function getTurnaround(): ?string
	{
		return $this->turnaround;
	}

	/**
	 * @return mixed
	 */
	public function setTurnaround(?string $turnaround): self
	{
		$this->turnaround = $turnaround;

		return $this;
	}

	public function getContractVolume(): ?string
	{
		return $this->contractVolume;
	}

	/**
	 * @return mixed
	 */
	public function setContractVolume(?string $contractVolume): self
	{
		$this->contractVolume = $contractVolume;

		return $this;
	}

	public function getCostCenterDepartments(): ?string
	{
		return $this->costCenterDepartments;
	}

	/**
	 * @return mixed
	 */
	public function setCostCenterDepartments(?string $costCenterDepartments): self
	{
		$this->costCenterDepartments = $costCenterDepartments;

		return $this;
	}

	public function getSalesTargetPersonaA(): ?string
	{
		return $this->salesTargetPersonaA;
	}

	/**
	 * @return mixed
	 */
	public function setSalesTargetPersonaA(?string $salesTargetPersonaA): self
	{
		$this->salesTargetPersonaA = $salesTargetPersonaA;

		return $this;
	}

	public function getSalesTargetPersonaB(): ?string
	{
		return $this->salesTargetPersonaB;
	}

	/**
	 * @return mixed
	 */
	public function setSalesTargetPersonaB(?string $salesTargetPersonaB): self
	{
		$this->salesTargetPersonaB = $salesTargetPersonaB;

		return $this;
	}

	public function getSalesTargetPersonaC(): ?string
	{
		return $this->salesTargetPersonaC;
	}

	/**
	 * @return mixed
	 */
	public function setSalesTargetPersonaC(?string $salesTargetPersonaC): self
	{
		$this->salesTargetPersonaC = $salesTargetPersonaC;

		return $this;
	}

	public function getContractAmount(): ?string
	{
		return $this->contractAmount;
	}

	/**
	 * @return mixed
	 */
	public function setContractAmount(?string $contractAmount): self
	{
		$this->contractAmount = $contractAmount;

		return $this;
	}

	public function getEndDateContract(): ?\DateTimeInterface
	{
		return $this->endDateContract;
	}

	/**
	 * @return mixed
	 */
	public function setEndDateContract(?\DateTimeInterface $endDateContract): self
	{
		$this->endDateContract = $endDateContract;

		return $this;
	}

	public function getGln(): ?string
	{
		return $this->gln;
	}

	/**
	 * @return mixed
	 */
	public function setGln(?string $gln): self
	{
		$this->gln = $gln;

		return $this;
	}

	public function getKpRegion(): ?string
	{
		return $this->kpRegion;
	}

	/**
	 * @return mixed
	 */
	public function setKpRegion(?string $kpRegion): self
	{
		$this->kpRegion = $kpRegion;

		return $this;
	}

	public function getVizientId(): ?string
	{
		return $this->vizientId;
	}

	/**
	 * @return mixed
	 */
	public function setVizientId(?string $vizientId): self
	{
		$this->vizientId = $vizientId;

		return $this;
	}

	public function getProjectCode(): ?string
	{
		return $this->projectCode;
	}

	/**
	 * @return mixed
	 */
	public function setProjectCode(?string $projectCode): self
	{
		$this->projectCode = $projectCode;

		return $this;
	}

	public function getReportingGroup(): ?string
	{
		return $this->reportingGroup;
	}

	/**
	 * @return mixed
	 */
	public function setReportingGroup(?string $reportingGroup): self
	{
		$this->reportingGroup = $reportingGroup;

		return $this;
	}

	public function getSalesCutOffDate(): ?\DateTimeInterface
	{
		return $this->salesCutOffDate;
	}

	/**
	 * @return mixed
	 */
	public function setSalesCutOffDate(?\DateTimeInterface $salesCutOffDate): self
	{
		$this->salesCutOffDate = $salesCutOffDate;

		return $this;
	}

	public function getXtmCid(): ?string
	{
		return $this->xtmCid;
	}

	/**
	 * @return mixed
	 */
	public function setXtmCid(?string $xtmCid): self
	{
		$this->xtmCid = $xtmCid;

		return $this;
	}

	public function getAccountType(): ?string
	{
		return $this->accountType;
	}

	/**
	 * @return mixed
	 */
	public function setAccountType(?string $accountType): self
	{
		$this->accountType = $accountType;

		return $this;
	}

	public function getAmAssigned(): ?string
	{
		return $this->amAssigned;
	}

	/**
	 * @return mixed
	 */
	public function setAmAssigned(?string $amAssigned): self
	{
		$this->amAssigned = $amAssigned;

		return $this;
	}

	public function getChartGroups(): ?array
	{
		return $this->chartGroups;
	}

	/**
	 * @return mixed
	 */
	public function setChartGroups(?array $chartGroups): self
	{
		$this->chartGroups = $chartGroups;

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

	public function getAccountencyContactPerson(): ?CustomerPerson
	{
		return $this->accountencyContactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setAccountencyContactPerson(?CustomerPerson $accountencyContactPerson): self
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

	public function getInHouseAmResponsible(): ?User
	{
		return $this->inHouseAmResponsible;
	}

	/**
	 * @return mixed
	 */
	public function setInHouseAmResponsible(?User $inHouseAmResponsible): self
	{
		$this->inHouseAmResponsible = $inHouseAmResponsible;

		return $this;
	}

	public function getInHousePcResponsible(): ?User
	{
		return $this->inHousePcResponsible;
	}

	/**
	 * @return mixed
	 */
	public function setInHousePcResponsible(?User $inHousePcResponsible): self
	{
		$this->inHousePcResponsible = $inHousePcResponsible;

		return $this;
	}

	public function getInHousePmResponsible(): ?User
	{
		return $this->inHousePmResponsible;
	}

	/**
	 * @return mixed
	 */
	public function setInHousePmResponsible(?User $inHousePmResponsible): self
	{
		$this->inHousePmResponsible = $inHousePmResponsible;

		return $this;
	}

	public function getInHouseSpResponsible(): ?User
	{
		return $this->inHouseSpResponsible;
	}

	/**
	 * @return mixed
	 */
	public function setInHouseSpResponsible(?User $inHouseSpResponsible): self
	{
		$this->inHouseSpResponsible = $inHouseSpResponsible;

		return $this;
	}

	public function getParentCustomer(): ?self
	{
		return $this->parentCustomer;
	}

	/**
	 * @return mixed
	 */
	public function setParentCustomer(?self $parentCustomer): self
	{
		$this->parentCustomer = $parentCustomer;

		return $this;
	}

	public function getUserGroup(): ?XtrfUserGroup
	{
		return $this->userGroup;
	}

	/**
	 * @return mixed
	 */
	public function setUserGroup(?XtrfUserGroup $userGroup): self
	{
		$this->userGroup = $userGroup;

		return $this;
	}

	public function getCustomerPortalPriceProfile(): ?CustomerPriceProfile
	{
		return $this->customerPortalPriceProfile;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerPortalPriceProfile(?CustomerPriceProfile $customerPortalPriceProfile): self
	{
		$this->customerPortalPriceProfile = $customerPortalPriceProfile;

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

	public function getIndustries(): Collection
	{
		return $this->industries;
	}

	/**
	 * @return mixed
	 */
	public function addIndustry(Industry $industry): self
	{
		if (!$this->industries->contains($industry)) {
			$this->industries[] = $industry;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeIndustry(Industry $industry): self
	{
		if ($this->industries->contains($industry)) {
			$this->industries->removeElement($industry);
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
			$customerPerson->addCustomer($this);
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
			$customerPerson->removeCustomer($this);
		}

		return $this;
	}

	public function getCustomerAccountencyPersons(): Collection
	{
		return $this->customerAccountencyPersons;
	}

	/**
	 * @return mixed
	 */
	public function addCustomerAccountencyPerson(CustomerAccountencyContactPerson $customerAccountencyPerson): self
	{
		if (!$this->customerAccountencyPersons->contains($customerAccountencyPerson)) {
			$this->customerAccountencyPersons[] = $customerAccountencyPerson;
			$customerAccountencyPerson->addCustomer($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomerAccountencyPerson(CustomerAccountencyContactPerson $customerAccountencyPerson): self
	{
		if ($this->customerAccountencyPersons->contains($customerAccountencyPerson)) {
			$this->customerAccountencyPersons->removeElement($customerAccountencyPerson);
			$customerAccountencyPerson->removeCustomer($this);
		}

		return $this;
	}

	public function getUsersPersonsResponsible(): Collection
	{
		return $this->usersPersonsResponsible;
	}

	/**
	 * @return mixed
	 */
	public function addUsersPersonsResponsible(User $usersPersonsResponsible): self
	{
		if (!$this->usersPersonsResponsible->contains($usersPersonsResponsible)) {
			$this->usersPersonsResponsible[] = $usersPersonsResponsible;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeUsersPersonsResponsible(User $usersPersonsResponsible): self
	{
		if ($this->usersPersonsResponsible->contains($usersPersonsResponsible)) {
			$this->usersPersonsResponsible->removeElement($usersPersonsResponsible);
		}

		return $this;
	}

	public function getLanguageSpecializations(): Collection
	{
		return $this->languageSpecializations;
	}

	/**
	 * @return mixed
	 */
	public function addLanguageSpecialization(LanguageSpecialization $languageSpecialization): self
	{
		if (!$this->languageSpecializations->contains($languageSpecialization)) {
			$this->languageSpecializations[] = $languageSpecialization;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLanguageSpecialization(LanguageSpecialization $languageSpecialization): self
	{
		if ($this->languageSpecializations->contains($languageSpecialization)) {
			$this->languageSpecializations->removeElement($languageSpecialization);
		}

		return $this;
	}

	public function getLanguages(): Collection
	{
		return $this->languages;
	}

	/**
	 * @return mixed
	 */
	public function addLanguage(XtrfLanguage $language): self
	{
		if (!$this->languages->contains($language)) {
			$this->languages[] = $language;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLanguage(XtrfLanguage $language): self
	{
		if ($this->languages->contains($language)) {
			$this->languages->removeElement($language);
		}

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
		$newCustomer = null === $settings ? null : $this;
		if ($settings->getCustomer() !== $newCustomer) {
			$settings->setCustomer($newCustomer);
		}

		return $this;
	}

	public function getContactPersons(): Collection
	{
		return $this->contactPersons;
	}

	/**
	 * @return mixed
	 */
	public function addContactPerson(CustomerPerson $contactPerson): self
	{
		if (!$this->contactPersons->contains($contactPerson)) {
			$this->contactPersons[] = $contactPerson;
			$contactPerson->setCustomer($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeContactPerson(CustomerPerson $contactPerson): self
	{
		if ($this->contactPersons->contains($contactPerson)) {
			$this->contactPersons->removeElement($contactPerson);
			// set the owning side to null (unless already changed)
			if ($contactPerson->getCustomer() === $this) {
				$contactPerson->setCustomer(null);
			}
		}

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

	public function getLastLoginDate(): ?\DateTimeInterface
	{
		return $this->lastLoginDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastLoginDate(?\DateTimeInterface $lastLoginDate): self
	{
		$this->lastLoginDate = $lastLoginDate;

		return $this;
	}

	public function getLastFailedLoginDate(): ?\DateTimeInterface
	{
		return $this->lastFailedLoginDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastFailedLoginDate(?\DateTimeInterface $lastFailedLoginDate): self
	{
		$this->lastFailedLoginDate = $lastFailedLoginDate;

		return $this;
	}

	public function getInvoices(): Collection
	{
		return $this->invoices;
	}

	/**
	 * @return mixed
	 */
	public function addInvoice(CustomerInvoice $invoice): self
	{
		if (!$this->invoices->contains($invoice)) {
			$this->invoices[] = $invoice;
			$invoice->setCustomer($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeInvoice(CustomerInvoice $invoice): self
	{
		if ($this->invoices->contains($invoice)) {
			$this->invoices->removeElement($invoice);
			// set the owning side to null (unless already changed)
			if ($invoice->getCustomer() === $this) {
				$invoice->setCustomer(null);
			}
		}

		return $this;
	}

	public function getQboId(): ?int
	{
		return $this->qboId;
	}

	/**
	 * @return mixed
	 */
	public function setQboId(?int $qboId): self
	{
		$this->qboId = $qboId;

		return $this;
	}

	public function getNewBusiness(): ?string
	{
		return $this->newBusiness;
	}

	/**
	 * @return mixed
	 */
	public function setNewBusiness(?string $newBusiness): self
	{
		$this->newBusiness = $newBusiness;

		return $this;
	}

	public function getComissionStartDate(): ?\DateTimeInterface
	{
		return $this->comissionStartDate;
	}

	/**
	 * @return mixed
	 */
	public function setComissionStartDate(?\DateTimeInterface $comissionStartDate): self
	{
		$this->comissionStartDate = $comissionStartDate;

		return $this;
	}

	public function getComissionStatusDate(): ?\DateTimeInterface
	{
		return $this->comissionStatusDate;
	}

	/**
	 * @return mixed
	 */
	public function setComissionStatusDate(?\DateTimeInterface $comissionStatusDate): self
	{
		$this->comissionStatusDate = $comissionStatusDate;

		return $this;
	}

	/**
	 * @return Collection|Project[]
	 */
	public function getProjects(): Collection
	{
		return $this->projects;
	}

	/**
	 * @return mixed
	 */
	public function addProject(Project $project): self
	{
		if (!$this->projects->contains($project)) {
			$this->projects[] = $project;
			$project->setCustomer($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProject(Project $project): self
	{
		if ($this->projects->removeElement($project)) {
			// set the owning side to null (unless already changed)
			if ($project->getCustomer() === $this) {
				$project->setCustomer(null);
			}
		}

		return $this;
	}

	public function getCategoryGroups(): ?array
	{
		return $this->categoryGroups;
	}

	public function setCategoryGroups(?array $categoryGroups): self
	{
		$this->categoryGroups = $categoryGroups;

		return $this;
	}

	public function getPermissions(): Collection
	{
		return $this->permissions;
	}

	/**
	 * @return mixed
	 */
	public function addPermission(Permission $permission): self
	{
		if (!$this->permissions->contains($permission)) {
			$this->permissions[] = $permission;
			$permission->setCpCustomer($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removePermission(Permission $permission): self
	{
		if ($this->permissions->contains($permission)) {
			$this->permissions->removeElement($permission);
			// set the owning side to null (unless already changed)
			if ($permission->getCpCustomer() === $this) {
				$permission->setCpCustomer(null);
			}
		}

		return $this;
	}

	public function getRoles(): ?array
	{
		return $this->roles ?? [];
	}

	/**
	 * @return mixed
	 */
	public function setRoles(?array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBlCustomer(): ?BlCustomer
	{
		return $this->blCustomer;
	}

	/**
	 * @return mixed
	 */
	public function setBlCustomer(?BlCustomer $blCustomer): self
	{
		// unset the owning side of the relation if necessary
		if (null === $blCustomer && null !== $this->blCustomer) {
			$this->blCustomer->setCustomer(null);
		}

		// set the owning side of the relation if necessary
		if (null !== $blCustomer && $blCustomer->getId() !== $this) {
			$blCustomer->setCustomer($this);
		}

		$this->blCustomer = $blCustomer;

		return $this;
	}

	public function getCreditNoteNumberingSchemaId(): ?string
	{
		return $this->creditNoteNumberingSchemaId;
	}

	public function setCreditNoteNumberingSchemaId(?string $creditNoteNumberingSchemaId): self
	{
		$this->creditNoteNumberingSchemaId = $creditNoteNumberingSchemaId;

		return $this;
	}

	public function getCreditNoteTemplateId(): ?string
	{
		return $this->creditNoteTemplateId;
	}

	public function setCreditNoteTemplateId(?string $creditNoteTemplateId): self
	{
		$this->creditNoteTemplateId = $creditNoteTemplateId;

		return $this;
	}

	public function getClientPortalInterfaceLocale(): ?string
	{
		return $this->clientPortalInterfaceLocale;
	}

	public function setClientPortalInterfaceLocale(?string $clientPortalInterfaceLocale): self
	{
		$this->clientPortalInterfaceLocale = $clientPortalInterfaceLocale;

		return $this;
	}

	public function isSendCcToEmail2(): ?bool
	{
		return $this->sendCcToEmail2;
	}

	public function isSendCcToEmail3(): ?bool
	{
		return $this->sendCcToEmail3;
	}

	public function isAddressSmsEnabled(): ?bool
	{
		return $this->addressSmsEnabled;
	}

	public function isCcInEmailsToContactPersons(): ?bool
	{
		return $this->ccInEmailsToContactPersons;
	}

	public function isNoCrmEmails(): ?bool
	{
		return $this->noCrmEmails;
	}

	public function isSinglePerson(): ?bool
	{
		return $this->singlePerson;
	}

	public function isUseDefaultUserGroup(): ?bool
	{
		return $this->useDefaultUserGroup;
	}

	public function isUseAddressAsCorrespondence(): ?bool
	{
		return $this->useAddressAsCorrespondence;
	}

	public function isNonPayer(): ?bool
	{
		return $this->nonPayer;
	}

	public function isValidTaxNo1(): ?bool
	{
		return $this->validTaxNo1;
	}

	public function isLimitAccessToPeopleResponsible(): ?bool
	{
		return $this->limitAccessToPeopleResponsible;
	}

	public function isUseDefaultCustomerServices(): ?bool
	{
		return $this->useDefaultCustomerServices;
	}

	public function isUseDefaultCustomerServicesWorkflows(): ?bool
	{
		return $this->useDefaultCustomerServicesWorkflows;
	}

	public function isActualDraftEndOfMonth(): ?bool
	{
		return $this->actualDraftEndOfMonth;
	}

	public function isActualFinalEndOfMonth(): ?bool
	{
		return $this->actualFinalEndOfMonth;
	}

	public function isExpectedDraftEndOfMonth(): ?bool
	{
		return $this->expectedDraftEndOfMonth;
	}

	public function isExpectedFinalEndOfMonth(): ?bool
	{
		return $this->expectedFinalEndOfMonth;
	}

	public function isUseDraft(): ?bool
	{
		return $this->useDraft;
	}

	public function isUseDefaultDatesCalculationRules(): ?bool
	{
		return $this->useDefaultDatesCalculationRules;
	}

	public function isCreateDefaultTermIfneeded(): ?bool
	{
		return $this->createDefaultTermIfneeded;
	}

	public function isCreateDefaultTmIfNeeded(): ?bool
	{
		return $this->createDefaultTmIfNeeded;
	}

	public function isFirstProjectDateAuto(): ?bool
	{
		return $this->firstProjectDateAuto;
	}

	public function isFirstQuoteDateAuto(): ?bool
	{
		return $this->firstQuoteDateAuto;
	}

	public function isUseDefaultCustomerLanguageSpecializations(): ?bool
	{
		return $this->useDefaultCustomerLanguageSpecializations;
	}

	public function isUseDefaultCustomerLanguages(): ?bool
	{
		return $this->useDefaultCustomerLanguages;
	}

	public function isSendInvoiceEmail(): ?bool
	{
		return $this->sendInvoiceEmail;
	}

	public function isBudgetCodeRequiredWhenAddingQuoteOrProject(): ?bool
	{
		return $this->budgetCodeRequiredWhenAddingQuoteOrProject;
	}

	public function isHasAvatar(): ?bool
	{
		return $this->hasAvatar;
	}

	public function getRules(): ?AVCustomerRule
	{
		return $this->rules;
	}

	public function setRules(?AVCustomerRule $rules): static
	{
		// unset the owning side of the relation if necessary
		if (null === $rules && null !== $this->rules) {
			$this->rules->setCustomer(null);
		}

		// set the owning side of the relation if necessary
		if (null !== $rules && $rules->getCustomer() !== $this) {
			$rules->setCustomer($this);
		}

		$this->rules = $rules;

		return $this;
	}

	/**
	 * @return Collection<int, CustomerService>
	 */
	public function getServices(): Collection
	{
		return $this->services;
	}

	public function addService(CustomerService $service): static
	{
		if (!$this->services->contains($service)) {
			$this->services->add($service);
		}

		return $this;
	}
}
