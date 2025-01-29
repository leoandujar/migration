<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_service_type')]
#[ORM\Index(name: '', columns: ['blservice_type_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['blservice_type_id'])]
#[ORM\Entity]
class BlServiceType implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_service_type_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_service_type_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'blservice_type_id', type: 'bigint', nullable: false)]
	private string $blServiceTypeId;

	#[ORM\Column(name: 'enabled', type: 'boolean', nullable: true)]
	private ?bool $enabled;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'bl_code', type: 'string', nullable: false)]
	private string $code;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlServiceTypeId(): ?string
	{
		return $this->blServiceTypeId;
	}

	public function setBlServiceTypeId(string $blServiceTypeId): self
	{
		$this->blServiceTypeId = $blServiceTypeId;

		return $this;
	}

	public function getEnabled(): ?bool
	{
		return $this->enabled;
	}

	public function setEnabled(?bool $enabled): self
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

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}
}
