<?php

namespace App\Model\Entity;

use App\Model\Repository\CustomFieldConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'custom_field_configuration')]
#[ORM\UniqueConstraint(name: 'custom_field_configuration_key_key', columns: ['key'])]
#[ORM\Entity(repositoryClass: CustomFieldConfigurationRepository::class)]
class CustomFieldConfiguration implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'custom_field_configuration_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'custom_field_configuration_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'default_value', type: 'text', nullable: true)]
	private ?string $defaultValue;

	#[ORM\Column(name: 'description', type: 'text', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'key', type: 'text', nullable: false)]
	private string $key;

	#[ORM\Column(name: 'name', type: 'text', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'number_precision', type: 'integer', nullable: true)]
	private ?int $numberPrecision;

	#[ORM\Column(name: 'selection_possible_values', type: 'text', nullable: true)]
	private ?string $selectionPossibleValues;

	#[ORM\Column(name: 'type', type: 'string', nullable: false)]
	private string $type;

	#[ORM\Column(name: 'preferences', type: 'string', nullable: false, options: ['default' => 'READ_WRITE::character varying'])]
	private string $preferences;

	#[ORM\Column(name: 'names_in_partner_portals', type: 'json', nullable: true)]
	private ?array $namesInPartnerPortals;

	#[ORM\Column(name: 'fields_names', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $fieldsNames;

	#[ORM\Column(name: 'services_option', type: 'text', nullable: false)]
	private string $servicesOption;

	#[ORM\Column(name: 'available_for_qrf', type: 'boolean', nullable: false)]
	private bool $availableForQrf;

	#[ORM\Column(name: 'available_for_customer_portal', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $availableForCustomerPortal;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): static
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): static
	{
		$this->version = $version;

		return $this;
	}

	public function getDefaultValue(): ?string
	{
		return $this->defaultValue;
	}

	public function setDefaultValue(?string $defaultValue): static
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function getKey(): ?string
	{
		return $this->key;
	}

	public function setKey(string $key): static
	{
		$this->key = $key;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getNumberPrecision(): ?int
	{
		return $this->numberPrecision;
	}

	public function setNumberPrecision(?int $numberPrecision): static
	{
		$this->numberPrecision = $numberPrecision;

		return $this;
	}

	public function getSelectionPossibleValues(): ?string
	{
		return $this->selectionPossibleValues;
	}

	public function setSelectionPossibleValues(?string $selectionPossibleValues): static
	{
		$this->selectionPossibleValues = $selectionPossibleValues;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(string $type): static
	{
		$this->type = $type;

		return $this;
	}

	public function getPreferences(): ?string
	{
		return $this->preferences;
	}

	public function setPreferences(string $preferences): static
	{
		$this->preferences = $preferences;

		return $this;
	}

	public function getNamesInPartnerPortals(): ?array
	{
		return $this->namesInPartnerPortals;
	}

	public function setNamesInPartnerPortals(?array $namesInPartnerPortals): static
	{
		$this->namesInPartnerPortals = $namesInPartnerPortals;

		return $this;
	}

	public function getFieldsNames(): ?array
	{
		return $this->fieldsNames;
	}

	public function setFieldsNames(?array $fieldsNames): static
	{
		$this->fieldsNames = $fieldsNames;

		return $this;
	}

	public function getServicesOption(): ?string
	{
		return $this->servicesOption;
	}

	public function setServicesOption(string $servicesOption): static
	{
		$this->servicesOption = $servicesOption;

		return $this;
	}

	public function isAvailableForQrf(): ?bool
	{
		return $this->availableForQrf;
	}

	public function setAvailableForQrf(bool $availableForQrf): static
	{
		$this->availableForQrf = $availableForQrf;

		return $this;
	}

	public function isAvailableForCustomerPortal(): ?bool
	{
		return $this->availableForCustomerPortal;
	}

	public function setAvailableForCustomerPortal(bool $availableForCustomerPortal): static
	{
		$this->availableForCustomerPortal = $availableForCustomerPortal;

		return $this;
	}
}
