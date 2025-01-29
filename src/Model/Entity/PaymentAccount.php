<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class PaymentAccount
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(type: 'guid')]
	private string $id;

	#[ORM\Column(type: 'integer')]
	private int $externalId;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $fullName;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $name;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $classification;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $type;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $subType;

	#[ORM\Column(type: 'boolean')]
	private bool $active;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createDate;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getExternalId(): ?int
	{
		return $this->externalId;
	}

	public function setExternalId(int $externalId): self
	{
		$this->externalId = $externalId;

		return $this;
	}

	public function getFullName(): ?string
	{
		return $this->fullName;
	}

	public function setFullName(?string $fullName): self
	{
		$this->fullName = $fullName;

		return $this;
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

	public function getClassification(): ?string
	{
		return $this->classification;
	}

	public function setClassification(string $classification): self
	{
		$this->classification = $classification;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getSubType(): ?string
	{
		return $this->subType;
	}

	public function setSubType(string $subType): self
	{
		$this->subType = $subType;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getCreateDate(): ?\DateTimeInterface
	{
		return $this->createDate;
	}

	public function setCreateDate(?\DateTimeInterface $createDate): self
	{
		$this->createDate = $createDate;

		return $this;
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
}
