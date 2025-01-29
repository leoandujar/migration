<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'cp_template')]
#[ORM\Index(columns: ['cp_template_id'])]
#[ORM\Index(columns: ['contact_person_id'])]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['type'])]
#[ORM\UniqueConstraint(name: '', columns: ['name', 'type', 'contact_person_id'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['name', 'type', 'contactPerson'], message: 'Name already exists under that type and id.')]
class CPTemplate
{
	public const TYPE_CONTACT_PERSON = 1;
	public const TYPE_CUSTOMER = 2;

	public const TARGET_ENTITY_PROJECT = 1;
	public const TARGET_ENTITY_QUOTE = 2;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'cp_template_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'cp_template_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: ContactPerson::class, inversedBy: 'cpTemplates')]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: false)]
	private ContactPerson $contactPerson;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'type', type: 'integer', nullable: false)]
	private int $type = self::TYPE_CONTACT_PERSON;

	#[ORM\Column(name: 'data', type: 'json', nullable: false)]
	private array $data;

	#[ORM\Column(name: 'data_new', type: 'json', nullable: true)]
	private ?array $dataNew;

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

	public function getType(): ?int
	{
		return $this->type;
	}

	public function setType(int $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getData(): ?array
	{
		return $this->data;
	}

	public function setData(array $data): self
	{
		$this->data = $data;

		return $this;
	}

	public function getContactPerson(): ?ContactPerson
	{
		return $this->contactPerson;
	}

	public function setContactPerson(?ContactPerson $contactPerson): self
	{
		$this->contactPerson = $contactPerson;

		return $this;
	}

	public function getDataNew(): ?array
	{
		return $this->dataNew;
	}

	public function setDataNew(?array $dataNew): static
	{
		$this->dataNew = $dataNew;

		return $this;
	}
}
