<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_role')]
#[ORM\Index(columns: ['code'])]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['target'])]
#[ORM\UniqueConstraint(name: '', columns: ['code', 'target'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['code', 'target'], message: 'Code already used for that target.')]
class Role
{
	public const ROLE_AP_ADMIN = 'ROLE_AP_ADMIN';
	public const ROLE_CP_ADMIN = 'ROLE_CP_ADMIN';
	public const ROLE_AP_PUBLIC = 'ROLE_AP_PUBLIC';
	public const ROLE_AP_BASE = 'ROLE_AP_BASE_ACCOUNT';
    public const ROLE_CP_BASE = 'ROLE_CP_BASE_ACCOUNT';
	public const ROLE_CP_PUBLIC_LOGIN = 'ROLE_CP_PUBLIC_LOGIN';
	public const TARGET_ADMIN_PORTAL = 1;
	public const TARGET_CLIENT_PORTAL = 2;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'role_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'role_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(type: 'string', length: 50)]
	private string $name;

	#[ORM\Column(name: 'code', type: 'string', length: 150)]
	private string $code;

	#[ORM\Column(name: 'target', type: 'integer', nullable: false, options: ['default' => 1])]
	private int $target = self::TARGET_ADMIN_PORTAL;

	#[ORM\Column(name: 'abilities', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $abilities = [];

	#[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'role', cascade: ['persist', 'remove'])]
	private mixed $permissions;

	public function __construct()
	{
		$this->permissions = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}

	public function getPermissions(): Collection
	{
		return $this->permissions;
	}

	public function addPermission(Permission $permission): self
	{
		if (!$this->permissions->contains($permission)) {
			$this->permissions[] = $permission;
			$permission->setRole($this);
		}

		return $this;
	}

	public function removePermission(Permission $permission): self
	{
		if ($this->permissions->contains($permission)) {
			$this->permissions->removeElement($permission);
			// set the owning side to null (unless already changed)
			if ($permission->getRole() === $this) {
				$permission->setRole(null);
			}
		}

		return $this;
	}

	public function getTarget(): ?int
	{
		return $this->target;
	}

	public function setTarget(int $target): self
	{
		$this->target = $target;

		return $this;
	}

	public function getAbilities(): ?array
	{
		return $this->abilities;
	}

	public function setAbilities(array $abilities): self
	{
		$this->abilities = $abilities;

		return $this;
	}
}
