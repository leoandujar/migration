<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Vendor
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(type: 'guid')]
	private string $id;

	#[ORM\Column(type: 'integer')]
	private int $externalId;

	#[ORM\Column(type: 'boolean')]
	private bool $vendor1099;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $companyName;

	#[ORM\Column(type: 'string', length: 255)]
	private string $displayName;

	#[ORM\Column(type: 'boolean')]
	private bool $active;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createDate;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\OneToMany(targetEntity: ProviderInvoice::class, mappedBy: 'vendor')]
	private mixed $outcomeDocuments;

	#[ORM\Column(type: 'boolean')]
	private bool $administrative;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->outcomeDocuments = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getVendor1099(): ?bool
	{
		return $this->vendor1099;
	}

	public function setVendor1099(bool $vendor1099): self
	{
		$this->vendor1099 = $vendor1099;

		return $this;
	}

	public function getCompanyName(): ?string
	{
		return $this->companyName;
	}

	public function setCompanyName(?string $companyName): self
	{
		$this->companyName = $companyName;

		return $this;
	}

	public function getDisplayName(): ?string
	{
		return $this->displayName;
	}

	public function setDisplayName(string $displayName): self
	{
		$this->displayName = $displayName;

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

	public function getExternalId(): ?int
	{
		return $this->externalId;
	}

	public function setExternalId(int $externalId): self
	{
		$this->externalId = $externalId;

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

	public function getOutcomeDocuments(): Collection
	{
		return $this->outcomeDocuments;
	}

	public function addOutcomeDocument(ProviderInvoice $outcomeDocument): self
	{
		if (!$this->outcomeDocuments->contains($outcomeDocument)) {
			$this->outcomeDocuments[] = $outcomeDocument;
			$outcomeDocument->setVendor($this);
		}

		return $this;
	}

	public function removeOutcomeDocument(ProviderInvoice $outcomeDocument): self
	{
		if ($this->outcomeDocuments->contains($outcomeDocument)) {
			$this->outcomeDocuments->removeElement($outcomeDocument);
			// set the owning side to null (unless already changed)
			if ($outcomeDocument->getVendor() === $this) {
				$outcomeDocument->setVendor(null);
			}
		}

		return $this;
	}

	public function getAdministrative(): ?bool
	{
		return $this->administrative;
	}

	public function setAdministrative(bool $administrative): self
	{
		$this->administrative = $administrative;

		return $this;
	}
}
