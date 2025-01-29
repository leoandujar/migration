<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_permission')]
#[ORM\Entity]
class Permission
{
	public const TARGET_ADMIN_PORTAL = 'internal';
	public const TARGET_CLIENT_PORTAL = 'customer';
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'permission_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'permission_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false)]
	private bool $active;

	#[ORM\ManyToOne(targetEntity: Action::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(name: 'action_id', referencedColumnName: 'action_id', nullable: false)]
	private Action $action;

	#[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'role_id', nullable: true)]
	private ?Role $role;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(name: 'internal_user_id', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $internalUser;

	#[ORM\ManyToOne(targetEntity: ContactPerson::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(name: 'cp_user_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?ContactPerson $cpUser;

	#[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(name: 'cp_customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $cpCustomer;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getAction(): ?Action
	{
		return $this->action;
	}

	/**
	 * @return mixed
	 */
	public function setAction(?Action $action): self
	{
		$this->action = $action;

		return $this;
	}

	public function getRole(): ?Role
	{
		return $this->role;
	}

	/**
	 * @return mixed
	 */
	public function setRole(?Role $role): self
	{
		$this->role = $role;

		return $this;
	}

	public function getInternalUser(): ?InternalUser
	{
		return $this->internalUser;
	}

	/**
	 * @return mixed
	 */
	public function setInternalUser(?InternalUser $internalUser): self
	{
		$this->internalUser = $internalUser;

		return $this;
	}

	public function getCpUser(): ?ContactPerson
	{
		return $this->cpUser;
	}

	/**
	 * @return mixed
	 */
	public function setCpUser(?ContactPerson $cpUser): self
	{
		$this->cpUser = $cpUser;

		return $this;
	}

	public function getCpCustomer(): ?Customer
	{
		return $this->cpCustomer;
	}

	/**
	 * @return mixed
	 */
	public function setCpCustomer(?Customer $cpCustomer): self
	{
		$this->cpUser = $cpCustomer;

		return $this;
	}
}
