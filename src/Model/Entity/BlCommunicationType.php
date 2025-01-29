<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_communication_type')]
#[ORM\Index(name: '', columns: ['blcommunication_type_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['bl_communication_type_id'])]
#[ORM\Entity]
class BlCommunicationType implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_communication_type_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_communication_type_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'blcommunication_type_id', type: 'bigint', nullable: false)]
	private string $blCommunicationTypeId;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\OneToMany(targetEntity: BlTranslationType::class, mappedBy: 'blCommunicationType', cascade: ['persist'])]
	private mixed $blTranslationTypes;

	public function __construct()
	{
		$this->translationTypes = new ArrayCollection();
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

	/**
	 * @return Collection|BlTranslationType[]
	 */
	public function getTranslationTypes(): Collection
	{
		return $this->blTranslationTypes;
	}

	public function addTranslationType(BlTranslationType $blTranslationType): self
	{
		if (!$this->blTranslationTypes->contains($blTranslationType)) {
			$this->blTranslationTypes[] = $blTranslationType;
			$blTranslationType->setBlCommunicationType($this);
		}

		return $this;
	}

	public function removeTranslationType(BlTranslationType $blTranslationType): self
	{
		if ($this->blTranslationTypes->removeElement($blTranslationType)) {
			// set the owning side to null (unless already changed)
			if ($blTranslationType->getBlCommunicationType() === $this) {
				$blTranslationType->setBlCommunicationType(null);
			}
		}

		return $this;
	}

	public function getBlCommunicationTypeId(): ?string
	{
		return $this->blCommunicationTypeId;
	}

	public function setBlCommunicationTypeId(string $blCommunicationTypeId): self
	{
		$this->blCommunicationTypeId = $blCommunicationTypeId;

		return $this;
	}
}
