<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_user')]
#[ORM\Index(columns: ['internal_user_id'])]
#[ORM\Index(columns: ['email'])]
#[ORM\Index(columns: ['username'])]
#[ORM\Index(columns: ['status'])]
#[ORM\Index(columns: ['created'])]
#[ORM\UniqueConstraint(name: '', columns: ['email'])]
#[ORM\UniqueConstraint(name: '', columns: ['username'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\InternalUserRepository')]
#[UniqueEntity(fields: 'email', message: 'Email already used.')]
#[UniqueEntity(fields: 'username', message: 'Username already used.')]
class InternalUser implements UserInterface, PasswordAuthenticatedUserInterface
{
	public const STATUS_ACTIVE = 1;
	public const STATUS_INACTIVE = 2;

	public const TYPE_INTERNAL = 1;
	public const TYPE_PUBLIC = 2;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'internal_user_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'internal_user_id', type: 'bigint')]
	private int $id;

	#[ORM\Column(name: 'username', type: 'string', length: 50, nullable: false)]
	#[Assert\Length(min: 4, max: 20, minMessage: 'The username must be at least {{ limit }} characters long.', maxMessage: 'The username cannot be longer than {{ limit }} characters.')]
	#[Assert\Regex(pattern: "/(?=^[\\p{L}\\- \\'\\.]*\\d{0,1}[\\p{L}\\- \\'\\.]*\$)^[\\p{L}\\d\\- \\'\\.]{1,50}\$/u", message: 'The username contain not allowed characters.')]
	#[Assert\NotBlank]
	private string $username = '';

	#[ORM\Column(name: 'status', type: 'integer', nullable: false)]
	#[Assert\NotBlank]
	private int $status = self::STATUS_INACTIVE;

	#[ORM\Column(name: 'email', type: 'string', length: 64, nullable: false)]
	#[Assert\NotBlank]
	#[Assert\Email]
	private string $email;

	#[ORM\Column(name: 'created', type: 'datetime', nullable: false)]
	protected \DateTimeInterface $created;

	#[ORM\Column(name: 'first_name', type: 'string', length: 50, nullable: false)]
	#[Assert\Length(min: 3, max: 50, minMessage: 'The first name must be at least {{ limit }} characters long.', maxMessage: 'The first name cannot be longer than {{ limit }} characters.')]
	#[Assert\Regex(pattern: "/(?=^[\\p{L}\\- \\'\\.]*\\d{0,1}[\\p{L}\\- \\'\\.]*\$)^[\\p{L}\\d\\- \\'\\.]{1,50}\$/u", message: 'The first name contain not allowed characters.')]
	#[Assert\NotBlank]
	private string $firstName = '';

	#[ORM\Column(name: 'last_name', type: 'string', length: 50, nullable: false)]
	#[Assert\Length(min: 3, max: 50, minMessage: 'The last name must be at least {{ limit }} characters long.', maxMessage: 'The last name cannot be longer than {{ limit }} characters.')]
	#[Assert\Regex(pattern: "/(?=^[\\p{L}\\- \\'\\.]*\\d{0,1}[\\p{L}\\- \\'\\.]*\$)^[\\p{L}\\d\\- \\'\\.]{1,50}\$/u", message: 'The last name contain not allowed characters.')]
	#[Assert\NotBlank]
	private string $lastName = '';

	#[ORM\Column(name: 'password', type: 'string', length: 200)]
	#[Assert\NotBlank]
	private string $password;

	#[ORM\Column(name: 'salt', type: 'string', length: 60, nullable: true)]
	private ?string $salt;

	#[ORM\Column(name: 'roles', type: 'json', length: 100, nullable: true)]
	private ?array $roles = [];

	#[ORM\Column(name: 'confirmation_token', type: 'string', length: 255, nullable: true)]
	private ?string $confirmationToken;

	#[ORM\Column(name: 'type', type: 'integer', nullable: true)]
	private ?int $type = self::TYPE_INTERNAL;

	#[ORM\Column(name: 'tag', type: 'json', length: 100, nullable: true)]
	private ?array $tag = [];

	#[ORM\Column(name: 'mobile', type: 'string', length: 25, nullable: true)]
	private ?string $mobile;

	#[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'internalUser', cascade: ['persist', 'remove'])]
	private mixed $permissions;

	#[ORM\Column(name: 'hs_owner_id', type: 'string', nullable: true)]
	private ?string $hsOwner;

	#[ORM\OneToMany(targetEntity: HsCustomer::class, mappedBy: 'owner', cascade: ['persist'])]
	private mixed $hsCustomers;

	#[ORM\OneToMany(targetEntity: HsDeal::class, mappedBy: 'owner', cascade: ['persist'])]
	private mixed $hsDeals;

	#[ORM\OneToMany(targetEntity: HsContactPerson::class, mappedBy: 'owner', cascade: ['persist'])]
	private mixed $hsContactPersons;

	#[ORM\OneToMany(targetEntity: HsEngagement::class, mappedBy: 'owner', cascade: ['persist'])]
	private mixed $hsEngagement;

	#[ORM\Column(name: 'last_login_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastLoginDate;

	#[ORM\Column(name: 'last_failed_login_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastFailedLoginDate;

	#[ORM\Column(name: 'cp_login_god_mode', type: 'boolean', nullable: true)]
	private ?bool $cpLoginGodMode;

	#[ORM\Column(name: 'cp_confirmation_token', type: 'string', length: 255, nullable: true)]
	private ?string $cpConfirmationToken;

	#[ORM\Column(name: 'cp_login_customers', type: 'json', nullable: true)]
	private ?array $cpLoginCustomers;

	#[ORM\Column(name: 'department', type: 'string', length: 255, nullable: true)]
	private ?string $department;

	#[ORM\Column(name: 'position', type: 'string', length: 255, nullable: true)]
	private ?string $position;

	#[ORM\Column(name: 'category_groups', type: 'json', nullable: true)]
	private ?array $categoryGroups;

	#[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: AVWorkflowMonitor::class)]
	private mixed $workflowMonitors;

	#[ORM\OneToMany(mappedBy: 'requestedBy', targetEntity: AvFlowMonitor::class)]
	private Collection $monitors;

	#[ORM\OneToMany(mappedBy: 'internalUser', targetEntity: APTemplate::class, cascade: ['persist', 'remove'])]
	private mixed $apTemplates;

	#[ORM\OneToOne(inversedBy: 'internalUser', targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'xtrf_user_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $xtrfUser;

	public function __construct()
	{
		$this->created = new \DateTime();
		$this->permissions = new ArrayCollection();
		$this->status = self::STATUS_INACTIVE;
		$this->hsCustomers = new ArrayCollection();
		$this->hsDeals = new ArrayCollection();
		$this->hsContactPersons = new ArrayCollection();
		$this->hsEngagement = new ArrayCollection();
		$this->workflowMonitors = new ArrayCollection();
		$this->apTemplates = new ArrayCollection();
        $this->monitors = new ArrayCollection();
	}

	public function __toString()
	{
		return "$this->firstName $this->lastName";
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function getUserIdentifier(): string
	{
		return $this->username;
	}

	/**
	 * @return mixed
	 */
	public function setUsername(string $username): self
	{
		$this->username = $username;

		return $this;
	}

	public function getStatus(): ?int
	{
		return $this->status;
	}

	/**
	 * @return mixed
	 */
	public function setStatus(int $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @return mixed
	 */
	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getCreated(): ?\DateTimeInterface
	{
		return $this->created;
	}

	/**
	 * @return mixed
	 */
	public function setCreated(\DateTimeInterface $created): self
	{
		$this->created = $created;

		return $this;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	/**
	 * @return mixed
	 */
	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	/**
	 * @return mixed
	 */
	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	/**
	 * @return mixed
	 */
	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	public function getSalt(): ?string
	{
		return $this->salt;
	}

	/**
	 * @return mixed
	 */
	public function setSalt(?string $salt): self
	{
		$this->salt = $salt;

		return $this;
	}

	public function getRoles(): array
	{
		return $this->roles;
	}

	/**
	 * @return mixed
	 */
	public function setRoles(?array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	public function getConfirmationToken(): ?string
	{
		return $this->confirmationToken;
	}

	/**
	 * @return mixed
	 */
	public function setConfirmationToken(?string $confirmationToken): self
	{
		$this->confirmationToken = $confirmationToken;

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
			$permission->setInternalUser($this);
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
			if ($permission->getInternalUser() === $this) {
				$permission->setInternalUser(null);
			}
		}

		return $this;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(?int $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function eraseCredentials(): void
	{
	}

	public function getTag(): ?array
	{
		return $this->tag;
	}

	/**
	 * @return mixed
	 */
	public function setTag(?array $tag): self
	{
		$this->tag = $tag;

		return $this;
	}

	public function getMobile(): ?string
	{
		return $this->mobile;
	}

	/**
	 * @return mixed
	 */
	public function setMobile(?string $mobile): self
	{
		$this->mobile = $mobile;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHsOwner(): ?string
	{
		return $this->hsOwner;
	}

	/**
	 * @return mixed
	 */
	public function setHsOwner(?string $hsOwner): self
	{
		$this->hsOwner = $hsOwner;

		return $this;
	}

	/**
	 * @return Collection|HsCustomer[]
	 */
	public function getHsCustomers(): Collection
	{
		return $this->hsCustomers;
	}

	/**
	 * @return mixed
	 */
	public function addHsCustomer(HsCustomer $hsCustomer): self
	{
		if (!$this->hsCustomers->contains($hsCustomer)) {
			$this->hsCustomers[] = $hsCustomer;
			$hsCustomer->setOwner($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeHsCustomer(HsCustomer $hsCustomer): self
	{
		if ($this->hsCustomers->removeElement($hsCustomer)) {
			// set the owning side to null (unless already changed)
			if ($hsCustomer->getOwner() === $this) {
				$hsCustomer->setOwner(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|HsDeal[]
	 */
	public function getHsDeals(): Collection
	{
		return $this->hsDeals;
	}

	/**
	 * @return mixed
	 */
	public function addHsDeal(HsDeal $hsDeal): self
	{
		if (!$this->hsDeals->contains($hsDeal)) {
			$this->hsDeals[] = $hsDeal;
			$hsDeal->setOwner($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeHsDeal(HsDeal $hsDeal): self
	{
		if ($this->hsDeals->removeElement($hsDeal)) {
			// set the owning side to null (unless already changed)
			if ($hsDeal->getOwner() === $this) {
				$hsDeal->setOwner(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|HsContactPerson[]
	 */
	public function getHsContactPersons(): Collection
	{
		return $this->hsContactPersons;
	}

	/**
	 * @return mixed
	 */
	public function addHsContactPerson(HsContactPerson $hsContactPerson): self
	{
		if (!$this->hsContactPersons->contains($hsContactPerson)) {
			$this->hsContactPersons[] = $hsContactPerson;
			$hsContactPerson->setOwner($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeHsContactPerson(HsContactPerson $hsContactPerson): self
	{
		if ($this->hsContactPersons->removeElement($hsContactPerson)) {
			// set the owning side to null (unless already changed)
			if ($hsContactPerson->getOwner() === $this) {
				$hsContactPerson->setOwner(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|HsEngagement[]
	 */
	public function getHsEngagement(): Collection
	{
		return $this->hsEngagement;
	}

	/**
	 * @return mixed
	 */
	public function addHsEngagement(HsEngagement $hsEngagement): self
	{
		if (!$this->hsEngagement->contains($hsEngagement)) {
			$this->hsEngagement[] = $hsEngagement;
			$hsEngagement->setOwner($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeHsEngagement(HsEngagement $hsEngagement): self
	{
		if ($this->hsEngagement->removeElement($hsEngagement)) {
			// set the owning side to null (unless already changed)
			if ($hsEngagement->getOwner() === $this) {
				$hsEngagement->setOwner(null);
			}
		}

		return $this;
	}

	public function __call(string $name, array $arguments): mixed
	{
		return $this->id;
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

	public function getCpLoginGodMode(): ?bool
	{
		return $this->cpLoginGodMode;
	}

	public function setCpLoginGodMode(?bool $cpLoginGodMode): self
	{
		$this->cpLoginGodMode = $cpLoginGodMode;

		return $this;
	}

	public function getCpConfirmationToken(): ?string
	{
		return $this->cpConfirmationToken;
	}

	public function setCpConfirmationToken(?string $cpConfirmationToken): self
	{
		$this->cpConfirmationToken = $cpConfirmationToken;

		return $this;
	}

	public function getCpLoginCustomers(): ?array
	{
		return $this->cpLoginCustomers;
	}

	public function setCpLoginCustomers(?array $cpLoginCustomers): self
	{
		$this->cpLoginCustomers = $cpLoginCustomers;

		return $this;
	}

	public function getPosition(): ?string
	{
		return $this->position;
	}

	public function setPosition(?string $position): self
	{
		$this->position = $position;

		return $this;
	}

	public function getDepartment(): ?string
	{
		return $this->department;
	}

	public function setDepartment(?string $department): self
	{
		$this->department = $department;

		return $this;
	}

	/**
	 * @return Collection<int, AVWorkflowMonitor>
	 */
	public function getWorkflowMonitors(): Collection
	{
		return $this->workflowMonitors;
	}

	public function addWorkflowMonitor(AVWorkflowMonitor $workflowMonitor): self
	{
		if (!$this->workflowMonitors->contains($workflowMonitor)) {
			$this->workflowMonitors[] = $workflowMonitor;
			$workflowMonitor->setCreatedBy($this);
		}

		return $this;
	}

	public function removeWorkflowMonitor(AVWorkflowMonitor $workflowMonitor): self
	{
		if ($this->workflowMonitors->removeElement($workflowMonitor)) {
			// set the owning side to null (unless already changed)
			if ($workflowMonitor->getCreatedBy() === $this) {
				$workflowMonitor->setCreatedBy(null);
			}
		}

		return $this;
	}

	public function isCpLoginGodMode(): ?bool
	{
		return $this->cpLoginGodMode;
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

	/**
	 * @return Collection<int, APTemplate>
	 */
	public function getApTemplates(): Collection
	{
		return $this->apTemplates;
	}

	public function addApTemplate(APTemplate $apTemplate): self
	{
		if (!$this->apTemplates->contains($apTemplate)) {
			$this->apTemplates->add($apTemplate);
			$apTemplate->setInternalUser($this);
		}

		return $this;
	}

	public function removeApTemplate(APTemplate $apTemplate): self
	{
		if ($this->apTemplates->removeElement($apTemplate)) {
			// set the owning side to null (unless already changed)
			if ($apTemplate->getInternalUser() === $this) {
				$apTemplate->setInternalUser(null);
			}
		}

		return $this;
	}

	public function getXtrfUser(): ?User
	{
		return $this->xtrfUser;
	}

	public function setXtrfUser(?User $xtrfUser): self
	{
		$this->xtrfUser = $xtrfUser;

		return $this;
	}

	public function getFullName()
	{
		return $this->firstName.' '.$this->lastName;
	}
}
