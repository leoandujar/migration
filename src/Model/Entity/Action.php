<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_action')]
#[ORM\Index(columns: ['code'])]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['target'])]
#[ORM\UniqueConstraint(name: '', columns: ['code', 'target'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['code', 'target'], message: 'Code already used for that target.')]
class Action
{
	public const TARGET_ADMIN_PORTAL = 1;
	public const TARGET_CLIENT_PORTAL = 2;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'action_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'action_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 50)]
	private string $name;

	#[ORM\Column(name: 'code', type: 'string', length: 150)]
	private string $code;

	#[ORM\Column(name: 'target', type: 'integer', nullable: false, options: ['default' => 1])]
	private int $target = self::TARGET_ADMIN_PORTAL;

	#[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'action', cascade: ['persist', 'remove'])]
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

	public function getPermissions(): Collection
	{
		return $this->permissions;
	}

	public function addPermission(Permission $permission): self
	{
		if (!$this->permissions->contains($permission)) {
			$this->permissions[] = $permission;
			$permission->setAction($this);
		}

		return $this;
	}

	public function removePermission(Permission $permission): self
	{
		if ($this->permissions->contains($permission)) {
			$this->permissions->removeElement($permission);
			// set the owning side to null (unless already changed)
			if ($permission->getAction() === $this) {
				$permission->setAction(null);
			}
		}

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

	public function getTarget(): ?int
	{
		return $this->target;
	}

	public function setTarget(int $target): self
	{
		$this->target = $target;

		return $this;
	}
}
