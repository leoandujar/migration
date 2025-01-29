<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use App\Connector\Xtrf\Response\Users\GetSingleResponse;

#[ORM\Table(name: 'xtrf_user')]
#[ORM\Entity]
class User implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'xtrf_user_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'xtrf_user_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'erased_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $erasedAt;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'allocate_licence', type: 'boolean', nullable: true)]
	private ?bool $allocateLicence;

	#[ORM\Column(name: 'email', type: 'string', nullable: false)]
	private string $email;

	#[ORM\Column(name: 'xtrf_password', type: 'string', nullable: true)]
	private ?string $xtrfPassword;

	#[ORM\Column(name: 'memoq_password', type: 'string', nullable: true)]
	private ?string $memoqPassword;

	#[ORM\Column(name: 'mobile_phone', type: 'string', nullable: true)]
	private ?string $mobilePhone;

	#[ORM\Column(name: 'phone', type: 'string', nullable: true)]
	private ?string $phone;

	#[ORM\Column(name: 'sms_enabled', type: 'boolean', nullable: true)]
	private ?bool $smsEnabled;

	#[ORM\Column(name: 'time_zone', type: 'string', nullable: true)]
	private ?string $timeZone;

	#[ORM\Column(name: 'linked_provider_id', type: 'bigint', nullable: true)]
	private ?string $linkedProviderId;

	#[ORM\Column(name: 'preferences_id', type: 'bigint', nullable: true)]
	private ?string $preferences;

	#[ORM\Column(name: 'preferred_social_media_contact_id', type: 'bigint', nullable: true)]
	private ?string $preferredSocialMediaContactId;

	#[ORM\Column(name: 'social_media_collection_id', type: 'bigint', nullable: true)]
	private ?string $socialMediaCollectionId;

	#[ORM\Column(name: 'has_legacy_authentication', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $hasLegacyAuthentication;

	#[ORM\Column(name: 'has_avatar', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $hasAvatar;

	#[ORM\Column(name: 'expiration_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $expirationDate;

	#[ORM\Column(name: 'first_name', type: 'string', nullable: false)]
	private string $firstName;

	#[ORM\Column(name: 'gender', type: 'string', nullable: true)]
	private ?string $gender;

	#[ORM\Column(name: 'initials', type: 'string', nullable: true)]
	private ?string $initials;

	#[ORM\Column(name: 'last_name', type: 'string', nullable: false)]
	private string $lastName;

	#[ORM\Column(name: 'xtrf_login', type: 'string', unique: true, nullable: false)]
	private string $login;

	#[ORM\ManyToOne(targetEntity: Branch::class)]
	#[ORM\JoinColumn(name: 'branch_id', referencedColumnName: 'branch_id', nullable: false)]
	private Branch $branch;

	#[ORM\Column(name: 'custom_fields_id', type: 'bigint', nullable: false, unique: true)]
	private string $customFieldsId;

	#[ORM\ManyToOne(targetEntity: PersonPosition::class)]
	#[ORM\JoinColumn(name: 'person_position_id', referencedColumnName: 'person_position_id', nullable: false)]
	private PersonPosition $position;

	#[ORM\ManyToOne(targetEntity: XtrfUserGroup::class)]
	#[ORM\JoinColumn(name: 'xtrf_user_group_id', referencedColumnName: 'xtrf_user_group_id', nullable: false)]
	private XtrfUserGroup $group;

	#[ORM\Column(name: 'owner_sf_id', type: 'text', nullable: true)]
	private ?string $ownerSfId;

	#[ORM\ManyToMany(targetEntity: Feedback::class, mappedBy: 'responsibleUsers', cascade: ['persist'])]
	private mixed $responsibleFeedbacks;

	#[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'usersPersonsResponsible', cascade: ['persist'])]
	private mixed $customersAdditionalPersons;

	#[ORM\ManyToMany(targetEntity: Feedback::class, mappedBy: 'users', cascade: ['persist'])]
	private mixed $feedbacks;

	#[ORM\OneToOne(mappedBy: 'user', targetEntity: XtrfUserEntityImage::class, cascade: ['persist'])]
	private XtrfUserEntityImage $entityImage;

	#[ORM\OneToOne(mappedBy: 'xtrfUser', targetEntity: InternalUser::class)]
	private InternalUser $internalUser;

	public function __construct()
	{
		$this->responsibleFeedbacks = new ArrayCollection();
		$this->customersAdditionalPersons = new ArrayCollection();
		$this->feedbacks = new ArrayCollection();
		$this->hsCustomers = new ArrayCollection();
		$this->hsContactPersons = new ArrayCollection();
		$this->hsDeals = new ArrayCollection();
	}

	public function hashFromObject(): string
	{
		return md5("$this->firstName $this->lastName $this->email $this->group $this->position");
	}

	public function hashFromRemote($remoteSource): string
	{
		return md5("{$remoteSource->getFirstName()}{$remoteSource->getLastName()}{$remoteSource->getEmail()}{$remoteSource->getTeam()}{$remoteSource->getRole()}");
	}

	public function populateFromRemote(GetSingleResponse $remoteSource): void
	{
		$this
			->setFirstName($remoteSource->getFirstName())
			->setLastName($remoteSource->getLastName())
			->setEmail($remoteSource->getEmail())
			->setGroup($remoteSource->getTeam())
			->setPosition($remoteSource->getRole());
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getExpirationDate(): ?\DateTimeInterface
	{
		return $this->expirationDate;
	}

	public function setExpirationDate(?\DateTimeInterface $expirationDate): self
	{
		$this->expirationDate = $expirationDate;

		return $this;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getGender(): ?string
	{
		return $this->gender;
	}

	public function setGender(?string $gender): self
	{
		$this->gender = $gender;

		return $this;
	}

	public function getInitials(): ?string
	{
		return $this->initials;
	}

	public function setInitials(?string $initials): self
	{
		$this->initials = $initials;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function getLogin(): ?string
	{
		return $this->login;
	}

	public function setLogin(string $login): self
	{
		$this->login = $login;

		return $this;
	}

	public function getCustomFieldsId(): ?string
	{
		return $this->customFieldsId;
	}

	public function setCustomFieldsId(string $customFieldsId): self
	{
		$this->customFieldsId = $customFieldsId;

		return $this;
	}

	public function getBranch(): ?Branch
	{
		return $this->branch;
	}

	public function setBranch(?Branch $branch): self
	{
		$this->branch = $branch;

		return $this;
	}

	public function getErasedAt(): ?\DateTimeInterface
	{
		return $this->erasedAt;
	}

	public function setErasedAt(?\DateTimeInterface $erasedAt): self
	{
		$this->erasedAt = $erasedAt;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getAllocateLicence(): ?bool
	{
		return $this->allocateLicence;
	}

	public function setAllocateLicence(?bool $allocateLicence): self
	{
		$this->allocateLicence = $allocateLicence;

		return $this;
	}

	public function getXtrfPassword(): ?string
	{
		return $this->xtrfPassword;
	}

	public function setXtrfPassword(?string $xtrfPassword): self
	{
		$this->xtrfPassword = $xtrfPassword;

		return $this;
	}

	public function getMemoqPassword(): ?string
	{
		return $this->memoqPassword;
	}

	public function setMemoqPassword(?string $memoqPassword): self
	{
		$this->memoqPassword = $memoqPassword;

		return $this;
	}

	public function getMobilePhone(): ?string
	{
		return $this->mobilePhone;
	}

	public function setMobilePhone(?string $mobilePhone): self
	{
		$this->mobilePhone = $mobilePhone;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): self
	{
		$this->phone = $phone;

		return $this;
	}

	public function getSmsEnabled(): ?bool
	{
		return $this->smsEnabled;
	}

	public function setSmsEnabled(?bool $smsEnabled): self
	{
		$this->smsEnabled = $smsEnabled;

		return $this;
	}

	public function getTimeZone(): ?string
	{
		return $this->timeZone;
	}

	public function setTimeZone(?string $timeZone): self
	{
		$this->timeZone = $timeZone;

		return $this;
	}

	public function getLinkedProviderId(): ?string
	{
		return $this->linkedProviderId;
	}

	public function setLinkedProviderId(?string $linkedProviderId): self
	{
		$this->linkedProviderId = $linkedProviderId;

		return $this;
	}

	public function getPreferences(): ?string
	{
		return $this->preferences;
	}

	public function setPreferences(?string $preferences): self
	{
		$this->preferences = $preferences;

		return $this;
	}

	public function getPreferredSocialMediaContactId(): ?string
	{
		return $this->preferredSocialMediaContactId;
	}

	public function setPreferredSocialMediaContactId(?string $preferredSocialMediaContactId): self
	{
		$this->preferredSocialMediaContactId = $preferredSocialMediaContactId;

		return $this;
	}

	public function getSocialMediaCollectionId(): ?string
	{
		return $this->socialMediaCollectionId;
	}

	public function setSocialMediaCollectionId(?string $socialMediaCollectionId): self
	{
		$this->socialMediaCollectionId = $socialMediaCollectionId;

		return $this;
	}

	public function getHasLegacyAuthentication(): ?bool
	{
		return $this->hasLegacyAuthentication;
	}

	public function setHasLegacyAuthentication(bool $hasLegacyAuthentication): self
	{
		$this->hasLegacyAuthentication = $hasLegacyAuthentication;

		return $this;
	}

	public function getHasAvatar(): ?bool
	{
		return $this->hasAvatar;
	}

	public function setHasAvatar(bool $hasAvatar): self
	{
		$this->hasAvatar = $hasAvatar;

		return $this;
	}

	public function getOwnerSfId(): ?string
	{
		return $this->ownerSfId;
	}

	public function setOwnerSfId(?string $ownerSfId): self
	{
		$this->ownerSfId = $ownerSfId;

		return $this;
	}

	public function getResponsibleFeedbacks(): Collection
	{
		return $this->responsibleFeedbacks;
	}

	public function addResponsibleFeedback(Feedback $responsibleFeedback): self
	{
		if (!$this->responsibleFeedbacks->contains($responsibleFeedback)) {
			$this->responsibleFeedbacks[] = $responsibleFeedback;
			$responsibleFeedback->addResponsibleUser($this);
		}

		return $this;
	}

	public function removeResponsibleFeedback(Feedback $responsibleFeedback): self
	{
		if ($this->responsibleFeedbacks->contains($responsibleFeedback)) {
			$this->responsibleFeedbacks->removeElement($responsibleFeedback);
			$responsibleFeedback->removeResponsibleUser($this);
		}

		return $this;
	}

	public function getCustomersAdditionalPersons(): Collection
	{
		return $this->customersAdditionalPersons;
	}

	public function addCustomersAdditionalPerson(Customer $customersAdditionalPerson): self
	{
		if (!$this->customersAdditionalPersons->contains($customersAdditionalPerson)) {
			$this->customersAdditionalPersons[] = $customersAdditionalPerson;
			$customersAdditionalPerson->addUsersPersonsResponsible($this);
		}

		return $this;
	}

	public function removeCustomersAdditionalPerson(Customer $customersAdditionalPerson): self
	{
		if ($this->customersAdditionalPersons->contains($customersAdditionalPerson)) {
			$this->customersAdditionalPersons->removeElement($customersAdditionalPerson);
			$customersAdditionalPerson->removeUsersPersonsResponsible($this);
		}

		return $this;
	}

	public function getFeedbacks(): Collection
	{
		return $this->feedbacks;
	}

	public function addFeedback(Feedback $feedback): self
	{
		if (!$this->feedbacks->contains($feedback)) {
			$this->feedbacks[] = $feedback;
			$feedback->addUser($this);
		}

		return $this;
	}

	public function removeFeedback(Feedback $feedback): self
	{
		if ($this->feedbacks->contains($feedback)) {
			$this->feedbacks->removeElement($feedback);
			$feedback->removeUser($this);
		}

		return $this;
	}

	public function getEntityImage(): ?XtrfUserEntityImage
	{
		return $this->entityImage;
	}

	public function setEntityImage(?XtrfUserEntityImage $entityImage): self
	{
		$this->entityImage = $entityImage;

		// set (or unset) the owning side of the relation if necessary
		$newUser = null === $entityImage ? null : $this;
		if ($entityImage->getUser() !== $newUser) {
			$entityImage->setUser($newUser);
		}

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	public function isAllocateLicence(): ?bool
	{
		return $this->allocateLicence;
	}

	public function isSmsEnabled(): ?bool
	{
		return $this->smsEnabled;
	}

	public function isHasLegacyAuthentication(): ?bool
	{
		return $this->hasLegacyAuthentication;
	}

	public function isHasAvatar(): ?bool
	{
		return $this->hasAvatar;
	}

	public function getPosition(): ?string
	{
		return $this->position;
	}

	public function setPosition(?string $position): static
	{
		$this->position = $position;

		return $this;
	}

	public function getGroup(): ?string
	{
		return $this->group;
	}

	public function setGroup(string $group): static
	{
		$this->group = $group;

		return $this;
	}

	public function getInternalUser(): ?InternalUser
	{
		return $this->internalUser;
	}

	public function setInternalUser(?InternalUser $internalUser): static
	{
		// unset the owning side of the relation if necessary
		if (null === $internalUser && null !== $this->internalUser) {
			$this->internalUser->setXtrfUser(null);
		}

		// set the owning side of the relation if necessary
		if (null !== $internalUser && $internalUser->getXtrfUser() !== $this) {
			$internalUser->setXtrfUser($this);
		}

		$this->internalUser = $internalUser;

		return $this;
	}
}
