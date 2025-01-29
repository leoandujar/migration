<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_translation_type')]
#[ORM\Index(name: '', columns: ['bltranslation_type_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['bltranslation_type_id'])]
#[ORM\Entity]
class BlTranslationType implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_translation_type_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_translation_type_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'bltranslation_type_id', type: 'bigint', nullable: false)]
	private string $blTranslationTypeId;

	#[ORM\Column(name: 'is_appointment_translation_type', type: 'boolean', nullable: true)]
	private ?bool $isAppointmentTranslationType;

	#[ORM\Column(name: 'enabled', type: 'boolean', nullable: false)]
	private bool $enabled;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\ManyToOne(targetEntity: BlCommunicationType::class, inversedBy: 'blTranslationTypes')]
	#[ORM\JoinColumn(name: 'bl_communication_type_id', referencedColumnName: 'bl_communication_type_id', nullable: false)]
	private BlCommunicationType $blCommunicationType;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlTranslationTypeId(): ?string
	{
		return $this->blTranslationTypeId;
	}

	public function setBlTranslationTypeId(string $blTranslationTypeId): self
	{
		$this->blTranslationTypeId = $blTranslationTypeId;

		return $this;
	}

	public function getIsAppointmentTranslationType(): ?bool
	{
		return $this->isAppointmentTranslationType;
	}

	public function setIsAppointmentTranslationType(?bool $isAppointmentTranslationType): self
	{
		$this->isAppointmentTranslationType = $isAppointmentTranslationType;

		return $this;
	}

	public function getEnabled(): ?bool
	{
		return $this->enabled;
	}

	public function setEnabled(bool $enabled): self
	{
		$this->enabled = $enabled;

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

	public function getBlCommunicationType(): ?BlCommunicationType
	{
		return $this->blCommunicationType;
	}

	public function setBlCommunicationType(?BlCommunicationType $blCommunicationType): self
	{
		$this->blCommunicationType = $blCommunicationType;

		return $this;
	}
}
