<?php

namespace App\Model\Entity;

use App\Model\Repository\ContactPersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'contact_person')]
#[ORM\Entity(repositoryClass: ContactPersonRepository::class)]
class ContactPerson implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'contact_person_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'contact_person_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'erased_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $erasedAt;

	#[ORM\Column(name: 'last_login_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastLoginDate;

	#[ORM\Column(name: 'last_failed_login_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastFailedLoginDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'first_contact_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $firstContactDate;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'use_partner_address_as_address', type: 'boolean', nullable: true)]
	private ?bool $usePartnerAddressAsAddress;

	#[ORM\OneToOne(targetEntity: CustomField::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'custom_fields_id', referencedColumnName: 'custom_fields_id', nullable: false)]
	private CustomField $customFields;

	#[ORM\ManyToOne(targetEntity: PersonDepartment::class)]
	#[ORM\JoinColumn(name: 'person_department_id', referencedColumnName: 'person_department_id', nullable: true)]
	private ?PersonDepartment $personDepartment;

	#[ORM\ManyToOne(targetEntity: PersonPosition::class)]
	#[ORM\JoinColumn(name: 'person_position_id', referencedColumnName: 'person_position_id', nullable: true)]
	private ?PersonPosition $personPosition;

	#[ORM\Column(name: 'email', type: 'string', nullable: true)]
	private ?string $email;

	#[ORM\Column(name: 'address_email2', type: 'string', nullable: true)]
	private ?string $addressEmail2;

	#[ORM\Column(name: 'address_email3', type: 'string', nullable: true)]
	private ?string $addressEmail3;

	#[ORM\Column(name: 'fax', type: 'string', nullable: true)]
	private ?string $fax;

	#[ORM\Column(name: 'mobile_phone', type: 'string', nullable: true)]
	private ?string $mobilePhone;

	#[ORM\Column(name: 'phone', type: 'string', nullable: true)]
	private ?string $phone;

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

	#[ORM\Column(name: 'no_crm_emails', type: 'boolean', nullable: true)]
	private ?bool $noCrmEmails;

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

	#[ORM\Column(name: 'gender', type: 'string', nullable: true)]
	private ?string $gender;

	#[ORM\Column(name: 'initials', type: 'string', nullable: true)]
	private ?string $initials;

	#[ORM\Column(name: 'last_name', type: 'string', nullable: true)]
	private ?string $lastName;

	#[ORM\Column(name: 'last_name_normalized', type: 'string', nullable: true)]
	private ?string $lastNameNormalized;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'name_normalized', type: 'string', nullable: true)]
	private ?string $nameNormalized;

	#[ORM\OneToOne(targetEntity: SystemAccount::class)]
	#[ORM\JoinColumn(name: 'system_account_id', referencedColumnName: 'system_account_id', nullable: true)]
	private ?SystemAccount $systemAccount;

	#[ORM\ManyToOne(targetEntity: Country::class)]
	#[ORM\JoinColumn(name: 'address_country_id', referencedColumnName: 'country_id', nullable: true)]
	private ?Country $addressCountry;

	#[ORM\ManyToOne(targetEntity: Province::class)]
	#[ORM\JoinColumn(name: 'address_province_id', referencedColumnName: 'province_id', nullable: true)]
	private ?Province $addressProvince;

	#[ORM\Column(name: 'role', type: 'string', length: 32, nullable: true)]
	private ?string $role;

	#[ORM\Column(name: 'roles', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $roles = [];

	#[ORM\Column(name: 'preferred_social_media_contact_id', type: 'bigint', nullable: true)]
	private ?string $preferredSocialMediaContactId;

	#[ORM\Column(name: 'social_media_collection_id', type: 'bigint', nullable: true)]
	private ?string $socialMediaCollectionId;

	#[ORM\Column(name: 'has_avatar', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $hasAvatar;

	#[ORM\Column(name: 'contact_nuid', type: 'string', nullable: true)]
	private ?string $contactNuid;

	#[ORM\Column(name: 'preferences', type: 'json', nullable: true)]
	private ?array $preferences = [];

	#[ORM\Column(type: 'boolean', nullable: true)]
	private ?bool $twoFactorEnabled;

	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $chartGroups;

	#[ORM\Column(name: 'category_groups', type: 'json', nullable: true)]
	private ?array $categoryGroups;

	#[ORM\Column(name: 'profile_pic_name', type: 'string', length: 60, nullable: true)]
	private ?string $profilePicName;

	#[ORM\Column(name: 'recovery_pass_token', type: 'string', length: 60, nullable: true)]
	private ?string $recoveryPassToken;

	#[ORM\Column(name: 'public_authentication_token', type: 'string', length: 60, nullable: true)]
	private ?string $publicAuthenticationToken;

	#[ORM\JoinTable(name: 'person_native_languages')]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'language_id', referencedColumnName: 'xtrf_language_id')]
	#[ORM\ManyToMany(targetEntity: XtrfLanguage::class, inversedBy: 'contactPersons', cascade: ['persist'])]
	protected mixed $languages;

	#[ORM\JoinTable(name: 'contact_person_categories2')]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'project_category_id', referencedColumnName: 'category_id')]
	#[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'contactPersons', cascade: ['persist'])]
	protected mixed $categories;

	#[ORM\OneToMany(mappedBy: 'cpUser', targetEntity: Permission::class, cascade: ['persist', 'remove'])]
	private mixed $permissions;

	#[ORM\OneToOne(mappedBy: 'contactPerson', targetEntity: CustomerPerson::class, orphanRemoval: true)]
	private CustomerPerson $customersPerson;

	#[ORM\OneToMany(mappedBy: 'contactPerson', targetEntity: CPTemplate::class, cascade: ['persist', 'remove'])]
	private mixed $cpTemplates;

	#[ORM\OneToOne(mappedBy: 'id', targetEntity: ProviderPerson::class)]
	private ProviderPerson $provider;

	#[ORM\OneToMany(mappedBy: 'contactPerson', targetEntity: AVDashboard::class, cascade: ['persist', 'remove'])]
	private mixed $dashboard;

	public function __construct()
	{
		$this->languages = new ArrayCollection();
		$this->categories = new ArrayCollection();
		$this->permissions = new ArrayCollection();
		$this->cpTemplates = new ArrayCollection();
		$this->dashboard = new ArrayCollection();
	}

	/**
	 * Returns the password used to authenticate the user.
	 *
	 * This should be the encoded password. On authentication, a plain-text
	 * password will be salted, encoded, and then compared to this value.
	 *
	 * @return string The password
	 */
	public function getPassword(): ?string
	{
		return $this->systemAccount->getCpApiPassword();
	}

	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * This can return null if the password was not encoded using a salt.
	 *
	 * @return string|null The salt
	 */
	public function getSalt(): ?string
	{
		return null;
	}

	public function getRoles(): array
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
	 * Returns the username used to authenticate the user.
	 *
	 * @return string The username
	 */
	public function getUsername(): ?string
	{
		return $this->email;
	}

	public function getUserIdentifier(): string
	{
		return $this->id;
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials(): void
	{
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

	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(?bool $active): self
	{
		$this->active = $active;

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

	public function getUsePartnerAddressAsAddress(): ?bool
	{
		return $this->usePartnerAddressAsAddress;
	}

	/**
	 * @return mixed
	 */
	public function setUsePartnerAddressAsAddress(?bool $usePartnerAddressAsAddress): self
	{
		$this->usePartnerAddressAsAddress = $usePartnerAddressAsAddress;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @return mixed
	 */
	public function setEmail(?string $email): self
	{
		$this->email = $email;

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

	public function getFax(): ?string
	{
		return $this->fax;
	}

	/**
	 * @return mixed
	 */
	public function setFax(?string $fax): self
	{
		$this->fax = $fax;

		return $this;
	}

	public function getMobilePhone(): ?string
	{
		return $this->mobilePhone;
	}

	/**
	 * @return mixed
	 */
	public function setMobilePhone(?string $mobilePhone): self
	{
		$this->mobilePhone = $mobilePhone;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	/**
	 * @return mixed
	 */
	public function setPhone(?string $phone): self
	{
		$this->phone = $phone;

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

	public function getGender(): ?string
	{
		return $this->gender;
	}

	/**
	 * @return mixed
	 */
	public function setGender(?string $gender): self
	{
		$this->gender = $gender;

		return $this;
	}

	public function getInitials(): ?string
	{
		return $this->initials;
	}

	/**
	 * @return mixed
	 */
	public function setInitials(?string $initials): self
	{
		$this->initials = $initials;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	/**
	 * @return mixed
	 */
	public function setLastName(?string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function getLastNameNormalized(): ?string
	{
		return $this->lastNameNormalized;
	}

	/**
	 * @return mixed
	 */
	public function setLastNameNormalized(?string $lastNameNormalized): self
	{
		$this->lastNameNormalized = $lastNameNormalized;

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

	public function getNameNormalized(): ?string
	{
		return $this->nameNormalized;
	}

	/**
	 * @return mixed
	 */
	public function setNameNormalized(?string $nameNormalized): self
	{
		$this->nameNormalized = $nameNormalized;

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

	public function getContactNuid(): ?string
	{
		return $this->contactNuid;
	}

	/**
	 * @return mixed
	 */
	public function setContactNuid(?string $contactNuid): self
	{
		$this->contactNuid = $contactNuid;

		return $this;
	}

	public function getTwoFactorEnabled(): ?bool
	{
		return $this->twoFactorEnabled;
	}

	/**
	 * @return mixed
	 */
	public function setTwoFactorEnabled(?bool $twoFactorEnabled): self
	{
		$this->twoFactorEnabled = $twoFactorEnabled;

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

	public function getProfilePicName(): ?string
	{
		return $this->profilePicName;
	}

	/**
	 * @return mixed
	 */
	public function setProfilePicName(?string $profilePicName): self
	{
		$this->profilePicName = $profilePicName;

		return $this;
	}

	public function getRecoveryPassToken(): ?string
	{
		return $this->recoveryPassToken;
	}

	/**
	 * @return mixed
	 */
	public function setRecoveryPassToken(?string $recoveryPassToken): self
	{
		$this->recoveryPassToken = $recoveryPassToken;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function setPublicAuthenticationToken(?string $publicAuthenticationToken = null): self
	{
		$this->publicAuthenticationToken = $publicAuthenticationToken;

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

	public function getPersonDepartment(): ?PersonDepartment
	{
		return $this->personDepartment;
	}

	/**
	 * @return mixed
	 */
	public function setPersonDepartment(?PersonDepartment $personDepartment): self
	{
		$this->personDepartment = $personDepartment;

		return $this;
	}

	public function getPersonPosition(): ?PersonPosition
	{
		return $this->personPosition;
	}

	/**
	 * @return mixed
	 */
	public function setPersonPosition(?PersonPosition $personPosition): self
	{
		$this->personPosition = $personPosition;

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

	public function getCustomersPerson(): ?CustomerPerson
	{
		return $this->customersPerson;
	}

	/**
	 * @return mixed
	 */
	public function setCustomersPerson(?CustomerPerson $customersPerson): self
	{
		$this->customersPerson = $customersPerson;

		// set (or unset) the owning side of the relation if necessary
		$newContactPerson = null === $customersPerson ? null : $this;
		if ($customersPerson->getContactPerson() !== $newContactPerson) {
			$customersPerson->setContactPerson($newContactPerson);
		}

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
			$permission->setCpUser($this);
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
			if ($permission->getCpUser() === $this) {
				$permission->setCpUser(null);
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

	/**
	 * @return mixed
	 */
	public function getRole(): ?string
	{
		return $this->role;
	}

	/**
	 * @return mixed
	 */
	public function setRole(?string $role): self
	{
		$this->role = $role;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getProvider(): ?ProviderPerson
	{
		return $this->provider;
	}

	/**
	 * @return mixed
	 */
	public function setProvider(?ProviderPerson $provider): self
	{
		// unset the owning side of the relation if necessary
		if (null === $provider && null !== $this->provider) {
			$this->provider->setId(null);
		}

		// set the owning side of the relation if necessary
		if (null !== $provider && $provider->getId() !== $this) {
			$provider->setId($this);
		}

		$this->provider = $provider;

		return $this;
	}

	public function getPreferences(): ?array
	{
		return $this->preferences;
	}

	public function setPreferences(?array $preferences): self
	{
		$this->preferences = $preferences;

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

	public function getPublicAuthenticationToken(): ?string
	{
		return $this->publicAuthenticationToken;
	}

	/**
	 * @return Collection<int, CPTemplate>
	 */
	public function getCpTemplates(): Collection
	{
		return $this->cpTemplates;
	}

	public function addCpTemplate(CPTemplate $cpTemplate): static
	{
		if (!$this->cpTemplates->contains($cpTemplate)) {
			$this->cpTemplates->add($cpTemplate);
			$cpTemplate->setContactPerson($this);
		}

		return $this;
	}

	public function removeCpTemplate(CPTemplate $cpTemplate): static
	{
		if ($this->cpTemplates->removeElement($cpTemplate)) {
			// set the owning side to null (unless already changed)
			if ($cpTemplate->getContactPerson() === $this) {
				$cpTemplate->setContactPerson(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, AVDashboard>
	 */
	public function getDashboard(): Collection
	{
		return $this->dashboard;
	}

	public function addDashboard(AVDashboard $dashboard): static
	{
		if (!$this->dashboard->contains($dashboard)) {
			$this->dashboard->add($dashboard);
			$dashboard->setContactPerson($this);
		}

		return $this;
	}

	public function removeDashboard(AVDashboard $dashboard): static
	{
		if ($this->dashboard->removeElement($dashboard)) {
			// set the owning side to null (unless already changed)
			if ($dashboard->getContactPerson() === $this) {
				$dashboard->setContactPerson(null);
			}
		}

		return $this;
	}
}
